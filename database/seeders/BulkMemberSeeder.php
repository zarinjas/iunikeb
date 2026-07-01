<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BulkMemberSeeder extends Seeder
{
    private array $firstNamesMale = [
        'Ahmad', 'Mohd', 'Muhammad', 'Aiman', 'Amirul', 'Azman', 'Faisal', 'Hafiz',
        'Harith', 'Haziq', 'Ikhwan', 'Irfan', 'Iskandar', 'Kamal', 'Khairul',
        'Megat', 'Nasir', 'Nik', 'Rahim', 'Rashid', 'Ridzuan', 'Saiful', 'Shahrul',
        'Syafiq', 'Syed', 'Tengku', 'Wan', 'Zainal', 'Zulkifli', 'Azhar', 'Baharin',
        'Fairuz', 'Hairul', 'Jamal', 'Khairi', 'Lokman', 'Nazri', 'Razak', 'Roslan',
        'Shamsul', 'Zulhilmi', 'Azim', 'Danish', 'Fikri', 'Hakim', 'Izzat', 'Luqman',
        'Muzafar', 'Najib', 'Rafiuddin', 'Syahmi', 'Asyraf', 'Farhan', 'Hanif',
        'Iqbal', 'Johan', 'Khalid', 'Muaz', 'Nazim', 'Rashdan',
    ];

    private array $firstNamesFemale = [
        'Aminah', 'Aisyah', 'Fatimah', 'Halimah', 'Khadijah', 'Mariam', 'Nurul',
        'Siti', 'Zaiton', 'Zarina', 'Azizah', 'Farah', 'Hafizah', 'Izzati',
        'Jamilah', 'Kartini', 'Mahani', 'Nadia', 'Noraini', 'Norsiah', 'Rohana',
        'Rosnani', 'Salbiah', 'Sarah', 'Suzana', 'Wan', 'Zaleha', 'Zainab',
        'Anis', 'Balqis', 'Dalilah', 'Fatin', 'Husna', 'Iman', 'Liyana',
        'Maisarah', 'Najwa', 'Nur', 'Raihan', 'Syahirah', 'Athirah', 'Batrisyia',
        'Damia', 'Erika', 'Farisha', 'Hanna', 'Insyirah', 'Jannah', 'Khalisha',
        'Mardhiah', 'Natasha', 'Nina', 'Qistina', 'Shafiqah', 'Sofea', 'Tasnim',
        'Ummi', 'Zara', 'Amira', 'Dhia',
    ];

    private array $lastNames = [
        'Abdullah', 'Abdul Rahman', 'Abdul Rahim', 'Abdul Hamid', 'Abdul Aziz',
        'Abdul Karim', 'Abdul Malik', 'Abdul Manaf', 'Abdul Halim', 'Ibrahim',
        'Ismail', 'Mohd Nor', 'Mohd Noor', 'Mohd Ali', 'Mohd Yusof', 'Mohd Salleh',
        'Mohd Shariff', 'Osman', 'Rahman', 'Hashim', 'Hussein', 'Yahaya', 'Yaakob',
        'Mat', 'Hassan', 'Hussin', 'Sulaiman', 'Harun', 'Idris', 'Alias', 'Bakar',
        'Din', 'Jusoh', 'Kassim', 'Mahmud', 'Musa', 'Nik', 'Othman', 'Ramli',
        'Said', 'Talib', 'Yusoff', 'Zainuddin', 'Ahmad', 'Aripin', 'Deraman',
        'Embong', 'Ghani', 'Hamzah', 'Jamil', 'Khalid', 'Latif', 'Mamat', 'Noh',
        'Puteh', 'Rashid', 'Shafie', 'Tahir', 'Wahab', 'Yunus',
    ];

    private array $positions = [
        'Pegawai Tadbir', 'Pegawai Perkhidmatan', 'Pegawai Penyelidik',
        'Pensyarah', 'Guru', 'Pegawai Perubatan', 'Jurutera', 'Akauntan',
        'Pegawai Teknologi Maklumat', 'Pembantu Tadbir', 'Pembantu Am Pejabat',
        'Pegawai Pemasaran', 'Pegawai Sumber Manusia', 'Pegawai Kewangan',
        'Pegawai Undang-Undang', 'Pegawai Audit', 'Pegawai Perhubungan',
        'Eksekutif Jualan', 'Eksekutif Operasi', 'Penyelia',
    ];

    private array $departments = [
        'Pejabat Pendaftar', 'Fakulti Sains & Teknologi', 'Fakulti Perubatan',
        'Fakulti Ekonomi', 'Fakulti Pendidikan', 'Fakulti Kejuruteraan',
        'Fakulti Undang-Undang', 'Jabatan Sumber Manusia', 'Jabatan Kewangan',
        'Jabatan Teknologi Maklumat', 'Jabatan Pemasaran', 'Jabatan Operasi',
        'Unit Pentadbiran', 'Institut Penyelidikan',
        'Pusat Pembangunan Profesional', 'Bahagian Audit Dalam',
    ];

    private array $employers = [
        'UKM Bangi', 'UPM Serdang', 'UM Kuala Lumpur', 'UiTM Shah Alam',
        'USM Pulau Pinang', 'UTHM Batu Pahat', 'UTM Skudai', 'UUM Sintok',
        'Kementerian Pendidikan Malaysia', 'Kementerian Kesihatan Malaysia',
        'Kementerian Kewangan Malaysia', 'HCTM Kuala Lumpur',
        'Hospital Serdang', 'Hospital Kajang', 'Maybank Berhad',
        'CIMB Bank Berhad', 'Bank Negara Malaysia', 'Telekom Malaysia',
        'Tenaga Nasional Berhad', 'Pos Malaysia Berhad',
    ];

    private array $banks = ['Maybank', 'CIMB', 'Bank Islam', 'Bank Rakyat', 'Bank Simpanan Nasional', 'RHB Bank', 'Public Bank', 'Hong Leong Bank', 'AmBank', 'Bank Muamalat'];

    private array $maritalStatuses = ['Bujang', 'Berkahwin', 'Bercerai', 'Duda/Janda'];

    private array $ethnicities = ['Melayu', 'Cina', 'India', 'Bumiputera Lain', 'Lain-lain'];

    private array $relations = ['Anak kandung', 'Anak tiri', 'Adik beradik', 'Ibu kandung', 'Bapa kandung', 'Pasangan', 'Lain-lain'];

    private array $photoColors = [
        [15, 118, 110], [29, 78, 216], [124, 58, 237], [217, 119, 6], [220, 38, 38],
        [8, 145, 178], [13, 148, 136], [192, 38, 211], [234, 88, 12], [22, 163, 74],
        [59, 130, 246], [236, 72, 153], [79, 70, 229], [20, 184, 166], [249, 115, 22],
    ];

    private array $states = [
        'Selangor', 'Kuala Lumpur', 'Putrajaya', 'Negeri Sembilan', 'Melaka',
        'Johor', 'Pahang', 'Perak', 'Kedah', 'Pulau Pinang', 'Perlis',
        'Kelantan', 'Terengganu', 'Sabah', 'Sarawak', 'Labuan',
    ];

    private array $citiesByState = [
        'Selangor' => ['Bangi', 'Kajang', 'Shah Alam', 'Subang Jaya', 'Petaling Jaya', 'Klang', 'Ampang', 'Cheras', 'Seri Kembangan', 'Rawang', 'Selayang', 'Gombak'],
        'Kuala Lumpur' => ['Kuala Lumpur', 'Cheras', 'Wangsa Maju', 'Setapak', 'Sentul', 'Titiwangsa', 'Bukit Bintang'],
        'Putrajaya' => ['Putrajaya'],
        'Negeri Sembilan' => ['Seremban', 'Port Dickson', 'Nilai', 'Bahau', 'Tampin'],
        'Melaka' => ['Melaka', 'Ayer Keroh', 'Masjid Tanah', 'Jasin', 'Alor Gajah'],
        'Johor' => ['Johor Bahru', 'Skudai', 'Muar', 'Batu Pahat', 'Kulai', 'Pasir Gudang', 'Segamat', 'Kluang'],
        'Pahang' => ['Kuantan', 'Temerloh', 'Bentong', 'Raub', 'Jerantut', 'Pekan'],
        'Perak' => ['Ipoh', 'Taiping', 'Teluk Intan', 'Sitiawan', 'Manjung', 'Gopeng', 'Kampar'],
        'Kedah' => ['Alor Setar', 'Sungai Petani', 'Kulim', 'Langkawi', 'Jitra'],
        'Pulau Pinang' => ['George Town', 'Butterworth', 'Bukit Mertajam', 'Bayan Lepas', 'Nibong Tebal'],
        'Perlis' => ['Kangar'],
        'Kelantan' => ['Kota Bharu', 'Pasir Mas', 'Tanah Merah', 'Gua Musang', 'Bachok'],
        'Terengganu' => ['Kuala Terengganu', 'Kemaman', 'Dungun', 'Marang'],
        'Sabah' => ['Kota Kinabalu', 'Sandakan', 'Tawau', 'Lahad Datu', 'Keningau', 'Ranau'],
        'Sarawak' => ['Kuching', 'Miri', 'Sibu', 'Bintulu', 'Sarikei', 'Samarahan'],
        'Labuan' => ['Labuan'],
    ];

    private array $postcodeRanges = [
        'Selangor' => [40000, 68000],
        'Kuala Lumpur' => [50000, 60000],
        'Putrajaya' => [62000, 62999],
        'Negeri Sembilan' => [70000, 73999],
        'Melaka' => [75000, 78999],
        'Johor' => [79000, 87000],
        'Pahang' => [25000, 28999],
        'Perak' => [30000, 36999],
        'Kedah' => [5000, 9999],
        'Pulau Pinang' => [10000, 14999],
        'Perlis' => [1000, 2999],
        'Kelantan' => [15000, 18999],
        'Terengganu' => [20000, 24999],
        'Sabah' => [88000, 91999],
        'Sarawak' => [93000, 98999],
        'Labuan' => [87000, 87999],
    ];

    private array $tamanNames = [
        'Taman Sri', 'Taman Mutiara', 'Taman Bahagia', 'Taman Indah', 'Taman Harmoni',
        'Taman Suria', 'Taman Mesra', 'Taman Saujana', 'Taman Seri', 'Taman Damai',
        'Taman Kenanga', 'Taman Melati', 'Taman Ria', 'Taman Jaya', 'Taman Wawasan',
        'Taman Bukit', 'Taman Impian', 'Taman Cemerlang', 'Taman Gemilang', 'Taman Bestari',
    ];

    private array $streetNames = [
        'Universiti', 'Pendidikan', 'Sains', 'Teknologi', 'Perindustrian',
        'Perdagangan', 'Utama', 'Bahagia', 'Harmoni', 'Damai', 'Indah',
        'Mutiara', 'Kasturi', 'Cempaka', 'Kenanga', 'Melati', 'Anggerik',
        'Orkid', 'Tanjung', 'Pelangi', 'Bukit', 'Pantai', 'Pinang',
        'Nipah', 'Ara', 'Pulasan', 'Rambutan', 'Cengal', 'Meranti',
        'Keruing', 'Intan', 'Berlian', 'Nilam', 'Delima', 'Zamrud',
    ];

    private array $bankPrefixes = [
        'Maybank' => ['51', '55'],
        'CIMB' => ['70', '86'],
        'Bank Islam' => ['12', '14'],
        'Bank Rakyat' => ['21'],
        'Bank Simpanan Nasional' => ['03'],
        'RHB Bank' => ['10', '11'],
        'Public Bank' => ['39', '47'],
        'Hong Leong Bank' => ['60', '61'],
        'AmBank' => ['88', '89'],
        'Bank Muamalat' => ['13'],
    ];

    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) {
            return;
        }

        $admin = User::query()->where('email', 'admin@iunikeb.com.my')->first();
        $password = Hash::make('password');

        Storage::disk('public')->makeDirectory('member-photos');

        for ($i = 1; $i <= 100; $i++) {
            $email = 'member'.str_pad($i, 2, '0', STR_PAD_LEFT).'@iunikeb.com.my';

            if (User::query()->where('email', $email)->exists()) {
                continue;
            }

            $gender = $this->randomElement(['male', 'female']);
            $fullName = $this->generateMalaysianName($gender);
            $state = $this->randomElement($this->states);
            $city = $this->randomElement($this->citiesByState[$state]);
            $postcode = $this->generatePostcode($state);
            $ic = $this->generateIC();
            $phone = $this->generatePhone();
            $dob = $this->icToDob($ic);
            $position = $this->randomElement($this->positions);
            $department = $this->randomElement($this->departments);
            $employer = $this->randomElement($this->employers);
            $bank = $this->randomElement($this->banks);
            $hasSpouse = $this->randomElement(['yes', 'no', 'yes', 'yes', 'no']) === 'yes';
            $maritalStatus = $hasSpouse ? 'Berkahwin' : $this->randomElement(['Bujang', 'Bujang', 'Bujang', 'Bercerai', 'Duda/Janda']);
            $salary = round($this->randomFloat(2500, 15000), 2);
            $ethnicity = $this->randomElement($this->ethnicities);
            $joinedMonths = random_int(1, 36);

            $street = $this->generateStreet();

            $user = User::query()->create([
                'cooperative_id' => $cooperative->id,
                'name' => $fullName,
                'email' => $email,
                'role' => User::ROLE_MEMBER,
                'user_type' => User::ROLE_MEMBER,
                'status' => 'active',
                'password' => $password,
                'phone' => $phone,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles([AccessControl::ROLE_MEMBER]);

            $color = $this->photoColors[$i % count($this->photoColors)];
            $photoPath = 'member-photos/bulk-'.str_pad($i, 3, '0', STR_PAD_LEFT).'.jpg';
            $this->generateProfilePhoto(storage_path('app/public/'.$photoPath), $fullName, $color);

            $memberNo = 'MBR-'.str_pad($i, 4, '0', STR_PAD_LEFT);

            $joinedAt = now()->subMonths($joinedMonths);
            $nextOfKinName = $this->generateNextOfKinName($gender);
            $nextOfKinRelation = 'Anak kandung';

            if ($this->randomElement(['a', 'b', 'c', 'd']) === 'a') {
                $nextOfKinRelation = $this->randomElement($this->relations);
            }

            $memberData = [
                'cooperative_id' => $cooperative->id,
                'user_id' => $user->id,
                'member_no' => $memberNo,
                'profile_photo_path' => $photoPath,
                'full_name' => $fullName,
                'identity_no' => $ic,
                'email' => $email,
                'phone' => $phone,
                'address_line_1' => $street,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode,
                'country' => 'Malaysia',
                'date_of_birth' => $dob,
                'gender' => $gender,
                'marital_status' => $maritalStatus,
                'ethnicity' => $ethnicity,
                'position' => $position,
                'department' => $department,
                'employer' => $employer,
                'employer_billing_address' => $this->generateStreet().', '.$postcode.' '.$city.', '.$state,
                'employment_no' => 'K'.str_pad(random_int(1, 99999), 6, '0', STR_PAD_LEFT),
                'salary' => $salary,
                'bank' => $bank,
                'bank_account' => $this->generateBankAccount($bank),
                'monthly_fee' => 100.00,
                'total_fee' => 1200.00,
                'special_savings' => round($this->randomFloat(1000, 50000), 2),
                'monthly_deduction' => round($this->randomFloat(50, 500), 2),
                'total_debt' => $this->randomElement([0, 0, 0, 0, round($this->randomFloat(1000, 50000), 2)]),
                'next_of_kin_name' => $nextOfKinName,
                'next_of_kin_relation' => $nextOfKinRelation,
                'next_of_kin_phone' => $this->generatePhone(),
                'next_of_kin_address' => $this->generateStreet().', '.$this->generatePostcode('Selangor').' '.$this->randomElement(['Bangi', 'Kajang', 'Seremban', 'Kuala Lumpur']).', '.$this->randomElement(['Selangor', 'Negeri Sembilan', 'Kuala Lumpur']),
                'beneficiary_address_line1' => $this->generateStreet(),
                'beneficiary_postcode' => $this->generatePostcode($state),
                'beneficiary_city' => $city,
                'beneficiary_state' => $state,
                'spouse_name' => null,
                'spouse_phone' => null,
                'spouse_address' => null,
                'spouse_address_line1' => null,
                'spouse_postcode' => null,
                'spouse_city' => null,
                'spouse_state' => null,
                'membership_status' => MemberStatus::Active->value,
                'joined_at' => $joinedAt,
                'approved_at' => $joinedAt,
                'approved_by' => $admin?->id,
                'portal_activated_at' => now(),
                'onboarding_completed_at' => now(),
            ];

            if ($hasSpouse) {
                $spouseGender = $gender === 'male' ? 'female' : 'male';
                $spouseName = $this->generateMalaysianName($spouseGender);
                $memberData['spouse_name'] = $spouseName;
                $memberData['spouse_phone'] = $this->generatePhone();
                $memberData['spouse_address'] = $this->generateStreet().', '.$postcode.' '.$city.', '.$state;
                $memberData['spouse_address_line1'] = $this->generateStreet();
                $memberData['spouse_postcode'] = $postcode;
                $memberData['spouse_city'] = $city;
                $memberData['spouse_state'] = $state;
            }

            Member::query()->create($memberData);
        }
    }

    private function randomElement(array $array): mixed
    {
        return $array[array_rand($array)];
    }

    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    private function generateMalaysianName(string $gender): string
    {
        $firstNames = $gender === 'male' ? $this->firstNamesMale : $this->firstNamesFemale;
        $firstName = $this->randomElement($firstNames);
        $lastName = $this->randomElement($this->lastNames);

        $titles = ['Megat', 'Nik', 'Syed', 'Tengku', 'Wan'];
        $binBinti = $gender === 'male' ? 'Bin' : 'Binti';

        if (in_array($firstName, $titles)) {
            $rest = $lastName;
            $useBin = $this->randomElement(['yes', 'no']) === 'yes';
            return $useBin ? $firstName.' '.$binBinti.' '.$rest : $firstName.' '.$rest;
        }

        $useBin = $this->randomElement(['yes', 'yes', 'no']) === 'yes';
        return $useBin
            ? $firstName.' '.$binBinti.' '.$lastName
            : $firstName.' '.$lastName;
    }

    private function generateIC(): string
    {
        $year = str_pad((string) random_int(60, 99), 2, '0', STR_PAD_LEFT);
        $month = str_pad((string) random_int(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) random_int(1, 28), 2, '0', STR_PAD_LEFT);
        $pb = str_pad((string) random_int(1, 59), 2, '0', STR_PAD_LEFT);
        $ss = str_pad((string) random_int(1, 99), 2, '0', STR_PAD_LEFT);
        $xxxx = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        return $year.$month.$day.$pb.$ss.$xxxx;
    }

    private function icToDob(string $ic): string
    {
        $y = substr($ic, 0, 2);
        $m = substr($ic, 2, 2);
        $d = substr($ic, 4, 2);
        $fullYear = ((int) $y > 25 ? '19' : '20').$y;
        return "$fullYear-$m-$d";
    }

    private function generatePhone(): string
    {
        $prefixes = ['011', '012', '013', '014', '016', '017', '018', '019'];
        $prefix = $this->randomElement($prefixes);
        $remaining = 11 - strlen($prefix);
        $digits = '';
        for ($j = 0; $j < $remaining; $j++) {
            $digits .= (string) random_int(0, 9);
        }
        return $prefix.$digits;
    }

    private function generatePostcode(string $state): string
    {
        $range = $this->postcodeRanges[$state] ?? [40000, 50000];
        return (string) random_int($range[0], $range[1]);
    }

    private function generateStreet(): string
    {
        $streetTypes = ['Jalan', 'Lorong', 'Persiaran'];
        $type = $this->randomElement($streetTypes);
        $name = $this->randomElement($this->streetNames);
        $num = random_int(1, 99);
        $taman = $this->randomElement($this->tamanNames);

        return 'No. '.$num.', '.$type.' '.$name.', '.$taman;
    }

    private function generateBankAccount(string $bank): string
    {
        $prefixes = $this->bankPrefixes[$bank] ?? ['12'];
        $prefix = $this->randomElement($prefixes);
        $rest = '';
        for ($j = 0; $j < 10; $j++) {
            $rest .= (string) random_int(0, 9);
        }
        return $prefix.$rest;
    }

    private function generateNextOfKinName(string $memberGender): string
    {
        $options = ['father', 'mother', 'sibling', 'sibling', 'child', 'child'];
        $option = $this->randomElement($options);

        return match ($option) {
            'father' => $this->generateMalaysianName('male'),
            'mother' => $this->generateMalaysianName('female'),
            'sibling' => $this->generateMalaysianName($this->randomElement(['male', 'female'])),
            'child' => $this->generateMalaysianName($this->randomElement(['male', 'female'])),
            default => $this->generateMalaysianName('female'),
        };
    }

    private function generateProfilePhoto(string $path, string $name, array $rgb): void
    {
        $size = 200;
        $image = imagecreatetruecolor($size, $size);

        $bg = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($image, 0, 0, $bg);

        $light = imagecolorallocatealpha($image, 255, 255, 255, 60);
        imagefilledellipse($image, $size / 2, $size / 2, 160, 160, $light);

        $white = imagecolorallocate($image, 255, 255, 255);

        $initial = mb_strtoupper(mb_substr($name, 0, 1));

        $fontPath = null;
        $fontCandidates = [
            '/System/Library/Fonts/Helvetica.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/TTF/DejaVuSans.ttf',
            '/usr/share/fonts/noto/NotoSans-Regular.ttf',
            'C:\Windows\Fonts\Arial.ttc',
            'C:\Windows\Fonts\Arial.ttf',
        ];

        foreach ($fontCandidates as $f) {
            if (file_exists($f)) {
                $fontPath = $f;
                break;
            }
        }

        if ($fontPath) {
            $fontSize = 72;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $initial);
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            $x = ($size - $textWidth) / 2;
            $y = ($size / 2) + ($textHeight / 2);
            imagettftext($image, $fontSize, 0, (int) $x, (int) $y, $white, $fontPath, $initial);
        } else {
            $fontSize = 5;
            $charWidth = imagefontwidth($fontSize);
            $x = ($size - $charWidth) / 2;
            $y = ($size - imagefontheight($fontSize)) / 2;
            imagestring($image, $fontSize, (int) $x, (int) $y, $initial, $white);
        }

        imagejpeg($image, $path, 85);
        imagedestroy($image);
    }
}
