<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IncubateeSeeder extends Seeder
{
    public function run()
    {
        $incubatees = [
            [
                'companyName'      => 'AgroSense AI',
                'slug'             => 'agrosense-ai',
                'founderName'      => 'Maria Elena Cruz',
                'shortDescription' => 'AI-powered crop monitoring platform that uses drone imagery and machine learning to detect plant diseases and optimize irrigation schedules for smallholder farmers.',
                'content'          => '<p>AgroSense AI develops precision agriculture tools that make advanced technology accessible to small-scale Filipino farmers. Their platform combines drone-captured multispectral imagery with custom machine learning models to provide actionable insights on crop health, soil moisture, and pest detection.</p><p>Founded in 2024 by Maria Elena Cruz, an agricultural engineering graduate from USTP, the startup has already piloted its solution across 50 hectares of rice paddies in Bukidnon, achieving a 23% reduction in water usage and 15% improvement in yield for participating farmers.</p><p>During incubation at ASOG TBI, the team refined their image processing pipeline and developed a mobile-first dashboard that works on low-bandwidth connections — critical for rural deployment.</p>',
                'logoPath'         => null,
                'websiteUrl'       => 'https://agrosense.ai',
                'cohort'           => 'Cohort 1 · 2024',
                'sortOrder'        => 1,
                'isPublished'      => 1,
            ],
            [
                'companyName'      => 'FreshChain',
                'slug'             => 'freshchain',
                'founderName'      => 'Jerico Villanueva & Ana Reyes',
                'shortDescription' => 'Blockchain-based supply chain traceability for fresh produce, connecting farmers directly to retailers with real-time quality monitoring.',
                'content'          => '<p>FreshChain tackles the massive post-harvest loss problem in Philippine agriculture — estimated at 30-40% for perishables. Their IoT-enabled tracking system monitors temperature, humidity, and handling throughout the supply chain, while blockchain ensures data integrity and transparency.</p><p>The platform allows consumers and retailers to scan a QR code on any product to see its complete journey from farm to shelf, including the farmer\'s profile, harvest date, and quality metrics at every transit point.</p><p>Through ASOG TBI, FreshChain secured partnerships with three major supermarket chains in CDO and developed their proprietary low-cost IoT sensor module that costs 80% less than commercial alternatives.</p>',
                'logoPath'         => null,
                'websiteUrl'       => 'https://freshchain.ph',
                'cohort'           => 'Cohort 1 · 2024',
                'sortOrder'        => 2,
                'isPublished'      => 1,
            ],
            [
                'companyName'      => 'CacaoTech',
                'slug'             => 'cacaotech',
                'founderName'      => 'Rafael Mendoza',
                'shortDescription' => 'Automated fermentation monitoring system for cacao processing that ensures consistent bean quality using sensor arrays and predictive algorithms.',
                'content'          => '<p>CacaoTech is revolutionizing Philippine cacao processing with their smart fermentation system. Traditional fermentation relies on experienced workers manually checking temperature and aroma — an inconsistent process that leads to variable chocolate quality.</p><p>Their sensor array monitors pH, temperature, humidity, and volatile organic compounds inside fermentation boxes in real-time. A predictive algorithm trained on data from master fermenters determines optimal turning schedules and endpoint detection.</p><p>The result: consistently high-quality fermented cacao beans that command premium prices in the specialty chocolate market. Early adopters report a 40% increase in bean value grade.</p>',
                'logoPath'         => null,
                'websiteUrl'       => null,
                'cohort'           => 'Cohort 1 · 2024',
                'sortOrder'        => 3,
                'isPublished'      => 1,
            ],
            [
                'companyName'      => 'PackGreen',
                'slug'             => 'packgreen',
                'founderName'      => 'Denise Lim',
                'shortDescription' => 'Biodegradable food packaging made from agricultural waste — banana stems and coconut husks transformed into compostable containers.',
                'content'          => '<p>PackGreen converts abundant agricultural waste into premium biodegradable food packaging. Using a proprietary process developed at USTP\'s materials lab, they transform banana pseudo-stems and coconut coir into molded containers that are grease-resistant, microwave-safe, and fully compostable within 90 days.</p><p>The packaging serves as a direct replacement for styrofoam and single-use plastics in food service. Each PackGreen container diverts approximately 200g of agricultural waste from open burning — a major source of air pollution in farming communities.</p><p>PackGreen graduated from ASOG-TBI with a production capacity of 10,000 units per day and supply agreements with 15 restaurants and catering businesses in Northern Mindanao.</p>',
                'logoPath'         => null,
                'websiteUrl'       => 'https://packgreen.co',
                'cohort'           => 'Cohort 2 · 2025',
                'sortOrder'        => 4,
                'isPublished'      => 1,
            ],
            [
                'companyName'      => 'AquaYield',
                'slug'             => 'aquayield',
                'founderName'      => 'Mark Anthony Roa',
                'shortDescription' => 'Smart aquaculture management system that monitors water quality parameters and automates feeding schedules for tilapia and milkfish ponds.',
                'content'          => '<p>AquaYield provides end-to-end pond management technology for fish farmers. Their solar-powered sensor nodes continuously measure dissolved oxygen, pH, ammonia, and temperature — the critical parameters that determine fish health and growth rates.</p><p>The system\'s AI engine learns each pond\'s unique characteristics and dynamically adjusts automated feeder schedules, aeration timing, and alerts for water quality anomalies. Farmers receive actionable notifications via SMS and the AquaYield mobile app.</p><p>In pilot deployments across 12 ponds in Misamis Oriental, AquaYield reduced feed waste by 25% and fish mortality by 60%, translating to an average income increase of ₱45,000 per harvest cycle for participating farmers.</p>',
                'logoPath'         => null,
                'websiteUrl'       => null,
                'cohort'           => 'Cohort 2 · 2025',
                'sortOrder'        => 5,
                'isPublished'      => 1,
            ],
            [
                'companyName'      => 'HarvestHub',
                'slug'             => 'harvesthub',
                'founderName'      => 'Christine Joy Abellana',
                'shortDescription' => 'Digital marketplace that connects smallholder farmers directly with institutional buyers, eliminating middlemen and ensuring fair pricing.',
                'content'          => '<p>HarvestHub is a B2B marketplace platform that solves the market access problem for smallholder farmers in Mindanao. By aggregating supply from farmer cooperatives and matching with demand from restaurants, hotels, hospitals, and school feeding programs, the platform ensures stable offtake at transparent prices.</p><p>The platform features real-time price discovery, quality grading standards, logistics coordination, and integrated digital payments. Farmers using HarvestHub report 20-35% higher farmgate prices compared to traditional middleman channels.</p><p>Currently active in CDO, Iligan, and Bukidnon with over 200 registered farmer cooperatives and 45 institutional buyers.</p>',
                'logoPath'         => null,
                'websiteUrl'       => 'https://harvesthub.ph',
                'cohort'           => 'Cohort 2 · 2025',
                'sortOrder'        => 6,
                'isPublished'      => 1,
            ],
        ];

        foreach ($incubatees as $inc) {
            $inc['createdAt'] = date('Y-m-d H:i:s');
            $inc['updatedAt'] = date('Y-m-d H:i:s');
            $this->db->table('incubatees')->insert($inc);
        }
    }
}