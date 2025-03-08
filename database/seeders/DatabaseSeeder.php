<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Subject;
use App\Models\ClassSiswa;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Administrator',
            'phone' => '08123456789',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin'
        ]);

        // Create Subjects
        Subject::insert([
            [
                'subject' => 'Mathematics',
                'description' => 'Advanced Mathematics for High School'
            ],
            [
                'subject' => 'Physics',
                'description' => 'Physics for Science Major'
            ],
            [
                'subject' => 'Biology',
                'description' => 'Biology for Science Major'
            ]
        ]);

        // Create Classes
        ClassSiswa::insert([
            [
                'class' => 'X',
                'description' => 'Kelas 10'
            ],
            [
                'class' => 'XI',
                'description' => 'Kelas 11'
            ],
            [
                'class' => 'XII',
                'description' => 'Kelas 12'
            ]
        ]);

        // Create Jurusan
        Jurusan::insert([
            [
                'jurusan' => 'IPA',
                'description' => 'Ilmu Pengetahuan Alam'
            ],
            [
                'jurusan' => 'IPS',
                'description' => 'Ilmu Pengetahuan Sosial'
            ],
            [
                'jurusan' => 'Bahasa',
                'description' => 'Bahasa dan Sastra'
            ]
        ]);

        // Create Guru with User
        $guruUser = User::create([
            'name' => 'guru',
            'phone' => '08123456790',
            'email' => 'johndoe@example.com',
            'username' => 'guru',
            'password' => Hash::make('guru'),
            'role' => 'guru'
        ]);

        // Get first subject for the guru
        $subject = Subject::first();

        Guru::create([
            'user_id' => $guruUser->user_id,
            'nip' => '198501012010011001',
            'subject_id' => $subject->subject_id,
            'education' => 'S2 Mathematics Education',
            'hire_date' => '2010-01-01',
            'gender' => 'L'
        ]);

        // Create Siswa with User
        $siswaUser = User::create([
            'name' => 'siswa',
            'phone' => '08123456791',
            'email' => 'janesmith@example.com',
            'username' => 'siswa',
            'password' => Hash::make('siswa'),
            'role' => 'siswa'
        ]);

        // Get first class and jurusan for the siswa
        $class = ClassSiswa::first();
        $jurusan = Jurusan::first();

        Siswa::create([
            'user_id' => $siswaUser->user_id,
            'nis' => '2024001',
            'tahun_masuk' => '2024',
            'class_id' => $class->class_id,
            'jurusan_id' => $jurusan->jurusan_id,
            'gender' => 'P'
        ]);
    }
}
