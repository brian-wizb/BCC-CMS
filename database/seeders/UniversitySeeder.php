<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            // ── Tanzania universities ────────────────────────────────────
            ['name' => 'University of Dar es Salaam (UDSM)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Muhimbili University of Health & Allied Sciences (MUHAS)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Sokoine University of Agriculture (SUA)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Ardhi University (ARU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Nelson Mandela African Institution of Science & Technology (NM-AIST)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'University of Dodoma (UDOM)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Mzumbe University', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Open University of Tanzania (OUT)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'St. Augustine University of Tanzania (SAUT)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Tumaini University Makumira (TUMA)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Iringa University', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Catholic University of Health & Allied Sciences (CUHAS)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Kilimanjaro Christian Medical University College (KCMUCo)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Institute of Finance Management (IFM)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Institute of Accountancy Arusha (IAA)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'College of Business Education (CBE)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Institute of Social Work (ISW)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Tanzania Institute of Accountancy (TIA)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Dar es Salaam Institute of Technology (DIT)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Arusha Technical College (ATC)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Moshi Co-operative University (MoCU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Ruaha Catholic University (RUCU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Sebastian Kolowa Memorial University (SEKOMU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'University of Arusha', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Jordan University College (JUCo)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Kampala International University – Tanzania (KIU-T)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'International Medical & Technological University (IMTU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Hubert Kairuki Memorial University (HKMU)', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Mount Meru University', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'Zanzibar University', 'country' => 'Tanzania', 'type' => 'local'],
            ['name' => 'State University of Zanzibar (SUZA)', 'country' => 'Tanzania', 'type' => 'local'],

            // ── Diaspora (Africa) ────────────────────────────────────────
            ['name' => 'Makerere University (Uganda)', 'country' => 'Uganda', 'type' => 'diaspora'],
            ['name' => 'University of Nairobi (Kenya)', 'country' => 'Kenya', 'type' => 'diaspora'],
            ['name' => 'Kenyatta University (Kenya)', 'country' => 'Kenya', 'type' => 'diaspora'],
            ['name' => 'University of Zambia (UNZA)', 'country' => 'Zambia', 'type' => 'diaspora'],
            ['name' => 'University of Zimbabwe', 'country' => 'Zimbabwe', 'type' => 'diaspora'],
            ['name' => 'University of Cape Town (UCT, South Africa)', 'country' => 'South Africa', 'type' => 'diaspora'],
            ['name' => 'University of the Witwatersrand (Wits, South Africa)', 'country' => 'South Africa', 'type' => 'diaspora'],
            ['name' => 'University of Ghana', 'country' => 'Ghana', 'type' => 'diaspora'],
            ['name' => 'Addis Ababa University (Ethiopia)', 'country' => 'Ethiopia', 'type' => 'diaspora'],
            ['name' => 'University of Rwanda', 'country' => 'Rwanda', 'type' => 'diaspora'],

            // ── Diaspora (Europe) ────────────────────────────────────────
            ['name' => 'University of Oxford (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University of Cambridge (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University College London (UCL, UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University of Edinburgh (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University of Manchester (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University of Birmingham (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'University of Bristol (UK)', 'country' => 'United Kingdom', 'type' => 'diaspora'],
            ['name' => 'Technische Universität Berlin (Germany)', 'country' => 'Germany', 'type' => 'diaspora'],
            ['name' => 'University of Hamburg (Germany)', 'country' => 'Germany', 'type' => 'diaspora'],
            ['name' => 'Ludwig Maximilian University of Munich (Germany)', 'country' => 'Germany', 'type' => 'diaspora'],
            ['name' => 'Utrecht University (Netherlands)', 'country' => 'Netherlands', 'type' => 'diaspora'],
            ['name' => 'Delft University of Technology (Netherlands)', 'country' => 'Netherlands', 'type' => 'diaspora'],
            ['name' => 'University of Amsterdam (Netherlands)', 'country' => 'Netherlands', 'type' => 'diaspora'],
            ['name' => 'KTH Royal Institute of Technology (Sweden)', 'country' => 'Sweden', 'type' => 'diaspora'],
            ['name' => 'Uppsala University (Sweden)', 'country' => 'Sweden', 'type' => 'diaspora'],
            ['name' => 'University of Oslo (Norway)', 'country' => 'Norway', 'type' => 'diaspora'],
            ['name' => 'University of Helsinki (Finland)', 'country' => 'Finland', 'type' => 'diaspora'],

            // ── Diaspora (North America) ─────────────────────────────────
            ['name' => 'Massachusetts Institute of Technology (MIT, USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'Harvard University (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'Stanford University (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'Yale University (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'Columbia University (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'University of Michigan (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'University of Texas at Austin (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'University of California, Los Angeles (UCLA, USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'Howard University (USA)', 'country' => 'United States', 'type' => 'diaspora'],
            ['name' => 'University of Toronto (Canada)', 'country' => 'Canada', 'type' => 'diaspora'],
            ['name' => 'McGill University (Canada)', 'country' => 'Canada', 'type' => 'diaspora'],
            ['name' => 'University of British Columbia (Canada)', 'country' => 'Canada', 'type' => 'diaspora'],

            // ── Diaspora (Asia/Middle East) ──────────────────────────────
            ['name' => 'University of Delhi (India)', 'country' => 'India', 'type' => 'diaspora'],
            ['name' => 'Indian Institute of Technology (IIT, India)', 'country' => 'India', 'type' => 'diaspora'],
            ['name' => 'Beijing Normal University (China)', 'country' => 'China', 'type' => 'diaspora'],
            ['name' => 'University of Dubai (UAE)', 'country' => 'United Arab Emirates', 'type' => 'diaspora'],
            ['name' => 'University of Malaya (Malaysia)', 'country' => 'Malaysia', 'type' => 'diaspora'],
        ];

        foreach ($universities as $uni) {
            University::firstOrCreate(['name' => $uni['name']], $uni);
        }
    }
}
