  <form method="POST" action="{{ route('mlm.register.store') }}" class="space-y-4">
    @csrf
    <input name="name" class="input" placeholder="Full name" required>
    <br>
    <input name="email" type="email" class="input" placeholder="Email" required>
    <br>
    <input name="username" class="input" placeholder="Username" required>
    <br>
    <input name="password" type="password" class="input" placeholder="Password" required>
    <br>
    <input name="password_confirmation" type="password" class="input" placeholder="Confirm password" required>
    <br>
    <input name="sponsor" class="input" placeholder="Sponsor username (optional)" value="{{ request('sponsor') }}">
    <br>
    <select name="side" class="input">
      <option value="">Auto (left spillover)</option>
      <option value="L" @selected(request('side')==='L')>Left</option>
      <option value="R" @selected(request('side')==='R')>Right</option>
    </select>
    <br>
    <button class="btn">Create Account</button>
  </form>
