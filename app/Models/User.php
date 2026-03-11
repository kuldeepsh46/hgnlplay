<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'username', 'member_id', 'mobile', 'pan_number', 'state', 'city', 'pincode', 'dob', 'address', 'account_number', 'account_holder_name', 'bank_name', 'branch_name', 'bank_address', 'ifsc_code', 'nominee_name', 'relation', 'nominee_dob', 'nominee_gender', 'sponsor_id', 'placement_id', 'sponsor_name', 'position', 'applied_for', 'wallet_balance', 'id_proof', 'address_proof', 'account_proof'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Automatic Member ID Generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Use max('id') to find the highest ID currently in the table
            $maxId = DB::table('users')->max('id') ?? 0;
            $nextId = $maxId + 1;

            // Format: HGNL + (10000 + next ID)
            // Example: If next ID is 1, member_id becomes HGNL10001
            $user->member_id = 'HGNL' . ($nextId + 10000);
        });
    }

    /* --- Relationships --- */

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function node()
    {
        return $this->hasOne(\App\Models\BinaryNode::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function walletLedgers()
    {
        // Renamed from wallet() to avoid conflict with wallets() relation
        return $this->hasMany(WalletLedger::class);
    }

    public function wallets()
    {
        return $this->hasOne(Wallet::class);
    }

    public function leftChild()
    {
        return $this->hasOne(User::class, 'placement_id')->where('position', 'left');
    }

    public function rightChild()
    {
        return $this->hasOne(User::class, 'placement_id')->where('position', 'right');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(User::class, 'sponsor_id')->with('childrenRecursive');
    }

    public function allDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->allDescendants());
        }

        return $descendants;
    }

    public function fundRequests()
    {
        return $this->hasMany(FundRequest::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
