-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 09, 2026 at 06:09 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u742712908_hgnl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('hgnlpay-cache-1037|2401:4900:b9a3:b90b:d429:864e:d371:315d', 'i:1;', 1774788493),
('hgnlpay-cache-1037|2401:4900:b9a3:b90b:d429:864e:d371:315d:timer', 'i:1774788493;', 1774788493),
('hgnlpay-cache-1038|2401:4900:b979:f131:703e:91ff:feea:abab', 'i:2;', 1774597587),
('hgnlpay-cache-1038|2401:4900:b979:f131:703e:91ff:feea:abab:timer', 'i:1774597587;', 1774597587),
('hgnlpay-cache-1038|2409:40d7:fe:7516:356f:ea6:24c0:f413', 'i:1;', 1774594732),
('hgnlpay-cache-1038|2409:40d7:fe:7516:356f:ea6:24c0:f413:timer', 'i:1774594732;', 1774594732),
('hgnlpay-cache-1098|2409:4056:203a:649::7a:50a1', 'i:1;', 1774779440),
('hgnlpay-cache-1098|2409:4056:203a:649::7a:50a1:timer', 'i:1774779440;', 1774779440),
('hgnlpay-cache-1149|103.217.118.113', 'i:1;', 1774527131),
('hgnlpay-cache-1149|103.217.118.113:timer', 'i:1774527131;', 1774527131),
('hgnlpay-cache-1181|2409:40d7:f4:33a9:8000::', 'i:1;', 1774702051),
('hgnlpay-cache-1181|2409:40d7:f4:33a9:8000:::timer', 'i:1774702051;', 1774702051),
('hgnlpay-cache-1182|2409:40d7:1038:b55a:c0cc:55ff:fe93:ae6b', 'i:2;', 1774606771),
('hgnlpay-cache-1182|2409:40d7:1038:b55a:c0cc:55ff:fe93:ae6b:timer', 'i:1774606771;', 1774606771),
('hgnlpay-cache-123|2409:40d7:1001:469:8000::', 'i:2;', 1774685545),
('hgnlpay-cache-123|2409:40d7:1001:469:8000:::timer', 'i:1774685545;', 1774685545),
('hgnlpay-cache-123ggnl1284|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602', 'i:1;', 1774953807),
('hgnlpay-cache-123ggnl1284|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602:timer', 'i:1774953807;', 1774953807),
('hgnlpay-cache-aabhisheksharma1002@gmail.com|2409:40d7:103b:aafe:8000::', 'i:1;', 1774536023),
('hgnlpay-cache-aabhisheksharma1002@gmail.com|2409:40d7:103b:aafe:8000:::timer', 'i:1774536023;', 1774536023),
('hgnlpay-cache-adm8448|2409:40d7:106f:7e2f:6077:85c5:58aa:b361', 'i:1;', 1774329033),
('hgnlpay-cache-adm8448|2409:40d7:106f:7e2f:6077:85c5:58aa:b361:timer', 'i:1774329033;', 1774329033),
('hgnlpay-cache-anandparas58@gmail.com|2404:7c80:24:1a51:cd0:920a:84d9:f1a1', 'i:1;', 1774852886),
('hgnlpay-cache-anandparas58@gmail.com|2404:7c80:24:1a51:cd0:920a:84d9:f1a1:timer', 'i:1774852886;', 1774852886),
('hgnlpay-cache-buntycba@gmail.com|103.217.118.113', 'i:2;', 1774527096),
('hgnlpay-cache-buntycba@gmail.com|103.217.118.113:timer', 'i:1774527096;', 1774527096),
('hgnlpay-cache-dsnekta@gmail.com|2409:40d7:ab:5fe:8456:39ff:fefa:c80a', 'i:1;', 1774760653),
('hgnlpay-cache-dsnekta@gmail.com|2409:40d7:ab:5fe:8456:39ff:fefa:c80a:timer', 'i:1774760653;', 1774760653),
('hgnlpay-cache-e. g hgnl|2401:4900:c8d:b6e8:0:5f:203b:e901', 'i:1;', 1775272798),
('hgnlpay-cache-e. g hgnl|2401:4900:c8d:b6e8:0:5f:203b:e901:timer', 'i:1775272798;', 1775272798),
('hgnlpay-cache-e. g. hgnl10001|2401:4900:c8d:b6e8:0:5f:203b:e901', 'i:1;', 1775272840),
('hgnlpay-cache-e. g. hgnl10001|2401:4900:c8d:b6e8:0:5f:203b:e901:timer', 'i:1775272840;', 1775272840),
('hgnlpay-cache-ggnl1284|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602', 'i:1;', 1774953834),
('hgnlpay-cache-ggnl1284|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602:timer', 'i:1774953834;', 1774953834),
('hgnlpay-cache-ghnl1126|2409:40d7:1033:2520:8000::', 'i:1;', 1774776255),
('hgnlpay-cache-ghnl1126|2409:40d7:1033:2520:8000:::timer', 'i:1774776255;', 1774776255),
('hgnlpay-cache-gk1980|2409:40d7:1036:c11a:8000::', 'i:1;', 1774942018),
('hgnlpay-cache-gk1980|2409:40d7:1036:c11a:8000:::timer', 'i:1774942018;', 1774942018),
('hgnlpay-cache-gnl1012|2409:40d7:47:51fa:f888:8fc1:154d:f542', 'i:1;', 1774948835),
('hgnlpay-cache-gnl1012|2409:40d7:47:51fa:f888:8fc1:154d:f542:timer', 'i:1774948835;', 1774948835),
('hgnlpay-cache-h g n l 1155|2404:7c80:24:1dee:2a2d:78c0:a422:a314', 'i:1;', 1774445826),
('hgnlpay-cache-h g n l 1155|2404:7c80:24:1dee:2a2d:78c0:a422:a314:timer', 'i:1774445826;', 1774445826),
('hgnlpay-cache-hagnl00010014|27.60.55.139', 'i:3;', 1774438438),
('hgnlpay-cache-hagnl00010014|27.60.55.139:timer', 'i:1774438438;', 1774438438),
('hgnlpay-cache-hg l1273|2409:40d7:a7:94af:8000::', 'i:1;', 1774682003),
('hgnlpay-cache-hg l1273|2409:40d7:a7:94af:8000:::timer', 'i:1774682003;', 1774682003),
('hgnlpay-cache-hg l1550u|2401:4900:43a4:4b8d:894:72ff:fe80:eeca', 'i:1;', 1775479176),
('hgnlpay-cache-hg l1550u|2401:4900:43a4:4b8d:894:72ff:fe80:eeca:timer', 'i:1775479176;', 1775479176),
('hgnlpay-cache-hgln1551|2409:4056:ec3:4c41:e48a:90ff:fe7e:c9', 'i:2;', 1775467697),
('hgnlpay-cache-hgln1551|2409:4056:ec3:4c41:e48a:90ff:fe7e:c9:timer', 'i:1775467697;', 1775467697),
('hgnlpay-cache-hgn1090|2401:4900:1c70:f388:4ca7:67dd:2033:a886', 'i:1;', 1775043566),
('hgnlpay-cache-hgn1090|2401:4900:1c70:f388:4ca7:67dd:2033:a886:timer', 'i:1775043566;', 1775043566),
('hgnlpay-cache-hgn1098|2409:40d7:106f:7e2f:5ce2:98be:a835:d763', 'i:1;', 1774439546),
('hgnlpay-cache-hgn1098|2409:40d7:106f:7e2f:5ce2:98be:a835:d763:timer', 'i:1774439546;', 1774439546),
('hgnlpay-cache-hgnk1123|2409:40d7:106f:f226:c5a0:a388:ff79:c502', 'i:1;', 1774508280),
('hgnlpay-cache-hgnk1123|2409:40d7:106f:f226:c5a0:a388:ff79:c502:timer', 'i:1774508280;', 1774508280),
('hgnlpay-cache-hgnl 1033|2409:4055:2d08:cccc:3a67:4c31:abff:e6f6', 'i:2;', 1774790803),
('hgnlpay-cache-hgnl 1033|2409:4055:2d08:cccc:3a67:4c31:abff:e6f6:timer', 'i:1774790803;', 1774790803),
('hgnlpay-cache-hgnl 1039|2409:40d7:fe:fd69:8000::', 'i:1;', 1775290456),
('hgnlpay-cache-hgnl 1039|2409:40d7:fe:fd69:8000:::timer', 'i:1775290456;', 1775290456),
('hgnlpay-cache-hgnl 1049|2409:40d7:eb:89c9:8000::', 'i:1;', 1774886802),
('hgnlpay-cache-hgnl 1049|2409:40d7:eb:89c9:8000:::timer', 'i:1774886802;', 1774886802),
('hgnlpay-cache-hgnl 1049|2409:40d7:fe:fd69:8000::', 'i:1;', 1775290504),
('hgnlpay-cache-hgnl 1049|2409:40d7:fe:fd69:8000:::timer', 'i:1775290504;', 1775290504),
('hgnlpay-cache-hgnl 1080|2409:40d1:480:540e:7d21:1154:d026:3aca', 'i:2;', 1774497537),
('hgnlpay-cache-hgnl 1080|2409:40d1:480:540e:7d21:1154:d026:3aca:timer', 'i:1774497537;', 1774497537),
('hgnlpay-cache-hgnl 1156|2404:7c80:24:1dee:2a2d:78c0:a422:a314', 'i:1;', 1774445851),
('hgnlpay-cache-hgnl 1156|2404:7c80:24:1dee:2a2d:78c0:a422:a314:timer', 'i:1774445851;', 1774445851),
('hgnlpay-cache-hgnl 1181|2409:40d7:f4:33a9:8000::', 'i:1;', 1774702124),
('hgnlpay-cache-hgnl 1181|2409:40d7:f4:33a9:8000:::timer', 'i:1774702124;', 1774702124),
('hgnlpay-cache-hgnl00010001|122.168.5.38', 'i:1;', 1774196947),
('hgnlpay-cache-hgnl00010001|122.168.5.38:timer', 'i:1774196947;', 1774196947),
('hgnlpay-cache-hgnl00010001|2409:40d7:4f:9e2a:3537:6c:6dd3:787f', 'i:2;', 1774059385),
('hgnlpay-cache-hgnl00010001|2409:40d7:4f:9e2a:3537:6c:6dd3:787f:timer', 'i:1774059385;', 1774059385),
('hgnlpay-cache-hgnl000100011|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b', 'i:1;', 1774086595),
('hgnlpay-cache-hgnl000100011|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b:timer', 'i:1774086595;', 1774086595),
('hgnlpay-cache-hgnl00010002|2409:40d7:106f:7e2f:5ce2:98be:a835:d763', 'i:1;', 1774439362),
('hgnlpay-cache-hgnl00010002|2409:40d7:106f:7e2f:5ce2:98be:a835:d763:timer', 'i:1774439362;', 1774439362),
('hgnlpay-cache-hgnl00010002|2409:40d7:106f:7e2f:c08e:f9c5:19c0:b759', 'i:1;', 1774176105),
('hgnlpay-cache-hgnl00010002|2409:40d7:106f:7e2f:c08e:f9c5:19c0:b759:timer', 'i:1774176105;', 1774176105),
('hgnlpay-cache-hgnl00010003|2401:4900:1c70:b032:fc38:2213:2ef8:baf7', 'i:3;', 1774087612),
('hgnlpay-cache-hgnl00010003|2401:4900:1c70:b032:fc38:2213:2ef8:baf7:timer', 'i:1774087612;', 1774087612),
('hgnlpay-cache-hgnl00010010|2409:40d7:100f:8fc9:8000::', 'i:1;', 1774448905),
('hgnlpay-cache-hgnl00010010|2409:40d7:100f:8fc9:8000:::timer', 'i:1774448905;', 1774448905),
('hgnlpay-cache-hgnl000100113|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b', 'i:1;', 1774088876),
('hgnlpay-cache-hgnl000100113|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b:timer', 'i:1774088876;', 1774088876),
('hgnlpay-cache-hgnl00010014|27.60.55.139', 'i:1;', 1774438748),
('hgnlpay-cache-hgnl00010014|27.60.55.139:timer', 'i:1774438748;', 1774438748),
('hgnlpay-cache-hgnl00010024|2409:40d7:106f:7e2f:6077:85c5:58aa:b361', 'i:2;', 1774329070),
('hgnlpay-cache-hgnl00010024|2409:40d7:106f:7e2f:6077:85c5:58aa:b361:timer', 'i:1774329070;', 1774329070),
('hgnlpay-cache-hgnl00010029|2409:40d7:106f:7e2f:95ec:3e9e:c448:9265', 'i:1;', 1774344857),
('hgnlpay-cache-hgnl00010029|2409:40d7:106f:7e2f:95ec:3e9e:c448:9265:timer', 'i:1774344857;', 1774344857),
('hgnlpay-cache-hgnl00010030|2409:40d7:4f:9e2a:3537:6c:6dd3:787f', 'i:2;', 1774059350),
('hgnlpay-cache-hgnl00010030|2409:40d7:4f:9e2a:3537:6c:6dd3:787f:timer', 'i:1774059350;', 1774059350),
('hgnlpay-cache-hgnl00010043|2409:40d7:106f:7e2f:d164:c35e:2cbb:f87c', 'i:3;', 1774266666),
('hgnlpay-cache-hgnl00010043|2409:40d7:106f:7e2f:d164:c35e:2cbb:f87c:timer', 'i:1774266666;', 1774266666),
('hgnlpay-cache-hgnl0001007|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b', 'i:1;', 1774073605),
('hgnlpay-cache-hgnl0001007|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b:timer', 'i:1774073605;', 1774073605),
('hgnlpay-cache-hgnl00010107|70.24.5.236', 'i:1;', 1775714746),
('hgnlpay-cache-hgnl00010107|70.24.5.236:timer', 'i:1775714746;', 1775714746),
('hgnlpay-cache-hgnl1000|2401:4900:1c70:f388:4ca7:67dd:2033:a886', 'i:1;', 1775043526),
('hgnlpay-cache-hgnl1000|2401:4900:1c70:f388:4ca7:67dd:2033:a886:timer', 'i:1775043526;', 1775043526),
('hgnlpay-cache-hgnl1000|70.24.5.236', 'i:2;', 1774329245),
('hgnlpay-cache-hgnl1000|70.24.5.236:timer', 'i:1774329245;', 1774329245),
('hgnlpay-cache-hgnl10001|2409:40d7:103b:aafe:8000::', 'i:1;', 1774536108),
('hgnlpay-cache-hgnl10001|2409:40d7:103b:aafe:8000:::timer', 'i:1774536108;', 1774536108),
('hgnlpay-cache-hgnl10002|2409:40d7:4a:20f9:b488:5862:467c:d744', 'i:1;', 1774362110),
('hgnlpay-cache-hgnl10002|2409:40d7:4a:20f9:b488:5862:467c:d744:timer', 'i:1774362110;', 1774362110),
('hgnlpay-cache-hgnl1001|2401:4900:1c6e:2902:e526:4037:661c:9aea', 'i:1;', 1774863199),
('hgnlpay-cache-hgnl1001|2401:4900:1c6e:2902:e526:4037:661c:9aea:timer', 'i:1774863199;', 1774863199),
('hgnlpay-cache-hgnl1001|2401:4900:8ff1:404d:b825:b19e:8b14:ff49', 'i:1;', 1774333483),
('hgnlpay-cache-hgnl1001|2401:4900:8ff1:404d:b825:b19e:8b14:ff49:timer', 'i:1774333483;', 1774333483),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:7e2f:6077:85c5:58aa:b361', 'i:2;', 1774332303),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:7e2f:6077:85c5:58aa:b361:timer', 'i:1774332303;', 1774332303),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:7e2f:6589:a283:5765:b46e', 'i:1;', 1774346955),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:7e2f:6589:a283:5765:b46e:timer', 'i:1774346955;', 1774346955),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:f226:c5a0:a388:ff79:c502', 'i:5;', 1774515019),
('hgnlpay-cache-hgnl1001|2409:40d7:106f:f226:c5a0:a388:ff79:c502:timer', 'i:1774515019;', 1774515019),
('hgnlpay-cache-hgnl1002|2409:4056:116:66e0:4ce2:ecc2:613e:ad5d', 'i:1;', 1775133338),
('hgnlpay-cache-hgnl1002|2409:4056:116:66e0:4ce2:ecc2:613e:ad5d:timer', 'i:1775133338;', 1775133338),
('hgnlpay-cache-hgnl1002|2409:40d7:49:8d44:f865:978b:2862:e409', 'i:3;', 1775215679),
('hgnlpay-cache-hgnl1002|2409:40d7:49:8d44:f865:978b:2862:e409:timer', 'i:1775215679;', 1775215679),
('hgnlpay-cache-hgnl1002|70.24.5.236', 'i:3;', 1775126798),
('hgnlpay-cache-hgnl1002|70.24.5.236:timer', 'i:1775126798;', 1775126798),
('hgnlpay-cache-hgnl10333|2409:40d7:106f:7e2f:5ce2:98be:a835:d763', 'i:1;', 1774441771),
('hgnlpay-cache-hgnl10333|2409:40d7:106f:7e2f:5ce2:98be:a835:d763:timer', 'i:1774441771;', 1774441771),
('hgnlpay-cache-hgnl1037|2401:4900:b9a3:b90b:d429:864e:d371:315d', 'i:2;', 1774788483),
('hgnlpay-cache-hgnl1037|2401:4900:b9a3:b90b:d429:864e:d371:315d:timer', 'i:1774788483;', 1774788483),
('hgnlpay-cache-hgnl108|2401:4900:43ac:5eff:0:63:1d8c:fd01', 'i:1;', 1775189103),
('hgnlpay-cache-hgnl108|2401:4900:43ac:5eff:0:63:1d8c:fd01:timer', 'i:1775189103;', 1775189103),
('hgnlpay-cache-hgnl1125ghnlhg|2409:40d7:a:61d0:8000::', 'i:1;', 1775465862),
('hgnlpay-cache-hgnl1125ghnlhg|2409:40d7:a:61d0:8000:::timer', 'i:1775465862;', 1775465862),
('hgnlpay-cache-hgnl11276|47.15.181.213', 'i:1;', 1774682630),
('hgnlpay-cache-hgnl11276|47.15.181.213:timer', 'i:1774682630;', 1774682630),
('hgnlpay-cache-hgnl1156|2404:7c80:24:1dee:2a2d:78c0:a422:a314', 'i:1;', 1774445801),
('hgnlpay-cache-hgnl1156|2404:7c80:24:1dee:2a2d:78c0:a422:a314:timer', 'i:1774445801;', 1774445801),
('hgnlpay-cache-hgnl1166|2409:40d7:106f:f226:c5a0:a388:ff79:c502', 'i:1;', 1774506720),
('hgnlpay-cache-hgnl1166|2409:40d7:106f:f226:c5a0:a388:ff79:c502:timer', 'i:1774506720;', 1774506720),
('hgnlpay-cache-hgnl1182|27.60.51.128', 'i:1;', 1774612332),
('hgnlpay-cache-hgnl1182|27.60.51.128:timer', 'i:1774612332;', 1774612332),
('hgnlpay-cache-hgnl1187|27.60.51.128', 'i:1;', 1774612515),
('hgnlpay-cache-hgnl1187|27.60.51.128:timer', 'i:1774612515;', 1774612515),
('hgnlpay-cache-hgnl1556|223.184.215.43', 'i:1;', 1774955972),
('hgnlpay-cache-hgnl1556|223.184.215.43:timer', 'i:1774955972;', 1774955972),
('hgnlpay-cache-hgnl1556|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602', 'i:1;', 1774955109),
('hgnlpay-cache-hgnl1556|2401:4900:b9ab:f3e8:94ba:cbff:fe0f:7602:timer', 'i:1774955109;', 1774955109),
('hgnlpay-cache-hgnl1566|70.24.5.236', 'i:1;', 1775714766),
('hgnlpay-cache-hgnl1566|70.24.5.236:timer', 'i:1775714766;', 1775714766),
('hgnlpay-cache-hgnl1575|27.60.55.68', 'i:2;', 1775037190),
('hgnlpay-cache-hgnl1575|27.60.55.68:timer', 'i:1775037190;', 1775037190),
('hgnlpay-cache-hgnl523|2401:4900:b96f:ab09::657:9e54', 'i:1;', 1774885917),
('hgnlpay-cache-hgnl523|2401:4900:b96f:ab09::657:9e54:timer', 'i:1774885917;', 1774885917),
('hgnlpay-cache-hgnl96|27.60.55.139', 'i:2;', 1774439732),
('hgnlpay-cache-hgnl96|27.60.55.139:timer', 'i:1774439732;', 1774439732),
('hgnlpay-cache-hgnlpay1125|2409:40d7:a:61d0:8000::', 'i:1;', 1775465935),
('hgnlpay-cache-hgnlpay1125|2409:40d7:a:61d0:8000:::timer', 'i:1775465935;', 1775465935),
('hgnlpay-cache-hhnl1435|2409:40d7:106a:2059:782e:7fac:60f7:f278', 'i:1;', 1774747578),
('hgnlpay-cache-hhnl1435|2409:40d7:106a:2059:782e:7fac:60f7:f278:timer', 'i:1774747578;', 1774747578),
('hgnlpay-cache-hngl1550|2401:4900:43a4:4b8d:894:72ff:fe80:eeca', 'i:1;', 1775480427),
('hgnlpay-cache-hngl1550|2401:4900:43a4:4b8d:894:72ff:fe80:eeca:timer', 'i:1775480427;', 1775480427),
('hgnlpay-cache-isrothakur@89gmail.com|2409:40d7:106f:7e2f:95ec:3e9e:c448:9265', 'i:3;', 1774344759),
('hgnlpay-cache-isrothakur@89gmail.com|2409:40d7:106f:7e2f:95ec:3e9e:c448:9265:timer', 'i:1774344759;', 1774344759),
('hgnlpay-cache-isrothakur89@gmail.com|2409:40d7:106f:7e2f:6589:a283:5765:b46e', 'i:1;', 1774345210),
('hgnlpay-cache-isrothakur89@gmail.com|2409:40d7:106f:7e2f:6589:a283:5765:b46e:timer', 'i:1774345210;', 1774345210),
('hgnlpay-cache-jagdish kumar|2401:4900:b9a1:c3f7::502:7c03', 'i:2;', 1774886179),
('hgnlpay-cache-jagdish kumar|2401:4900:b9a1:c3f7::502:7c03:timer', 'i:1774886179;', 1774886179),
('hgnlpay-cache-jaikalkama|2401:4900:b9b7:f25f:1c83:70ff:feb1:eec9', 'i:1;', 1775611992),
('hgnlpay-cache-jaikalkama|2401:4900:b9b7:f25f:1c83:70ff:feb1:eec9:timer', 'i:1775611992;', 1775611992),
('hgnlpay-cache-m01116|2401:4900:b9a1:c3f7::502:7c03', 'i:1;', 1774886211),
('hgnlpay-cache-m01116|2401:4900:b9a1:c3f7::502:7c03:timer', 'i:1774886211;', 1774886211),
('hgnlpay-cache-m01116|2401:4900:c8d:b6e8:0:5f:203b:e901', 'i:1;', 1775272759),
('hgnlpay-cache-m01116|2401:4900:c8d:b6e8:0:5f:203b:e901:timer', 'i:1775272759;', 1775272759),
('hgnlpay-cache-munnachauhan851@gmail.com|2401:4900:b972:ea96:d134:b:8716:7a8d', 'i:1;', 1774887462),
('hgnlpay-cache-munnachauhan851@gmail.com|2401:4900:b972:ea96:d134:b:8716:7a8d:timer', 'i:1774887462;', 1774887462),
('hgnlpay-cache-omshiv|2401:4900:b9b7:f25f:1c83:70ff:feb1:eec9', 'i:1;', 1775612163),
('hgnlpay-cache-omshiv|2401:4900:b9b7:f25f:1c83:70ff:feb1:eec9:timer', 'i:1775612163;', 1775612163),
('hgnlpay-cache-omshiv|2401:4900:b9b8:ea5b:fceb:caff:fe88:c789', 'i:1;', 1775624347),
('hgnlpay-cache-omshiv|2401:4900:b9b8:ea5b:fceb:caff:fe88:c789:timer', 'i:1775624347;', 1775624347),
('hgnlpay-cache-sanjay88|2401:4900:b933:e2d9::ffc:caa0', 'i:1;', 1775133911),
('hgnlpay-cache-sanjay88|2401:4900:b933:e2d9::ffc:caa0:timer', 'i:1775133911;', 1775133911),
('hgnlpay-cache-sapna@123|2409:40d7:1008:14fe:f830:91b4:a0ba:512f', 'i:1;', 1774453390),
('hgnlpay-cache-sapna@123|2409:40d7:1008:14fe:f830:91b4:a0ba:512f:timer', 'i:1774453390;', 1774453390),
('hgnlpay-cache-sapna|2409:40d7:1008:14fe:f830:91b4:a0ba:512f', 'i:1;', 1774453941),
('hgnlpay-cache-sapna|2409:40d7:1008:14fe:f830:91b4:a0ba:512f:timer', 'i:1774453941;', 1774453941),
('hgnlpay-cache-sapna22|2409:40d7:1008:14fe:f830:91b4:a0ba:512f', 'i:1;', 1774453407),
('hgnlpay-cache-sapna22|2409:40d7:1008:14fe:f830:91b4:a0ba:512f:timer', 'i:1774453407;', 1774453407),
('hgnlpay-cache-sapnabhat86@gmail.com|2409:40d7:1008:14fe:f830:91b4:a0ba:512f', 'i:1;', 1774453530),
('hgnlpay-cache-sapnabhat86@gmail.com|2409:40d7:1008:14fe:f830:91b4:a0ba:512f:timer', 'i:1774453530;', 1774453530),
('hgnlpay-cache-shivom|2401:4900:b9b8:ea5b:fceb:caff:fe88:c789', 'i:1;', 1775637497),
('hgnlpay-cache-shivom|2401:4900:b9b8:ea5b:fceb:caff:fe88:c789:timer', 'i:1775637497;', 1775637497),
('hgnlpay-cache-sonu|2401:4900:43a4:4b8d:894:72ff:fe80:eeca', 'i:1;', 1775479139),
('hgnlpay-cache-sonu|2401:4900:43a4:4b8d:894:72ff:fe80:eeca:timer', 'i:1775479139;', 1775479139),
('hgnlpay-cache-sunilthakur73778@gamil.com|122.161.72.203', 'i:1;', 1774887505),
('hgnlpay-cache-sunilthakur73778@gamil.com|122.161.72.203:timer', 'i:1774887505;', 1774887505),
('hgnlpay-cache-sunny1164|2409:40d7:106f:f226:c5a0:a388:ff79:c502', 'i:1;', 1774506880),
('hgnlpay-cache-sunny1164|2409:40d7:106f:f226:c5a0:a388:ff79:c502:timer', 'i:1774506880;', 1774506880),
('hgnlpay-cache-user4210|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b', 'i:1;', 1774084528),
('hgnlpay-cache-user4210|2409:40d7:57:e7ed:cdd5:71fa:f2ed:10b:timer', 'i:1774084528;', 1774084528),
('hgnlpay-cache-usr4210|2409:40d7:103a:c4e7:4d92:bab:ee35:2010', 'i:2;', 1774946015),
('hgnlpay-cache-usr4210|2409:40d7:103a:c4e7:4d92:bab:ee35:2010:timer', 'i:1774946015;', 1774946015),
('hgnlpay-cache-usr4210|2409:40d7:106f:7e2f:6077:85c5:58aa:b361', 'i:2;', 1774328887),
('hgnlpay-cache-usr4210|2409:40d7:106f:7e2f:6077:85c5:58aa:b361:timer', 'i:1774328887;', 1774328887),
('hgnlpay-cache-usr4210|2409:40d7:4a:20f9:b488:5862:467c:d744', 'i:1;', 1774362008),
('hgnlpay-cache-usr4210|2409:40d7:4a:20f9:b488:5862:467c:d744:timer', 'i:1774362008;', 1774362008),
('hgnlpay-cache-usr4210|70.24.5.236', 'i:1;', 1775714740),
('hgnlpay-cache-usr4210|70.24.5.236:timer', 'i:1775714740;', 1775714740),
('hgnlpay-cache-vishal@gmail.co.|2401:4900:8fe2:106f:29a8:8bc:28d4:7828', 'i:1;', 1774448559),
('hgnlpay-cache-vishal@gmail.co.|2401:4900:8fe2:106f:29a8:8bc:28d4:7828:timer', 'i:1774448559;', 1774448559),
('hgnlpay-cache-vishal@gmail.com|2409:40d7:b:4551:4530:7017:2996:9097', 'i:1;', 1774755090),
('hgnlpay-cache-vishal@gmail.com|2409:40d7:b:4551:4530:7017:2996:9097:timer', 'i:1774755090;', 1774755090);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
