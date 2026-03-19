<?php $pageTitle = 'Terms of Service — K2 Pickleball Platform'; ?>

<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <h1 class="font-display text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight animate-fade-in-up">
            Terms of <span class="gradient-gold">Service</span>
        </h1>
        <p class="mt-4 text-sm text-slate-400 animate-fade-in-up" style="animation-delay:0.1s">Last updated: <?= date('F j, Y') ?></p>
    </div>
</section>

<section class="py-16 lg:py-24 relative">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 prose-custom">
        <?php
        $sections = [
            ['Agreement to Terms', 'These Terms of Service ("Terms") constitute a legally binding agreement between you and K2 Pickleball Platform ("K2", "we", "us") governing your access to and use of our website, platform, and services. By accessing or using our services, you agree to be bound by these Terms. If you do not agree, do not use our services.'],
            ['Description of Services', 'K2 provides a cloud-based facility management platform for pickleball facilities, including court scheduling, membership management, payment processing, point-of-sale, event management, analytics, and related services. Access to the platform is provided through our partnership program under separately negotiated partnership agreements.'],
            ['Account Registration', 'To access certain features, you must create an account. You agree to:<br>• Provide accurate, current, and complete information<br>• Maintain and update your information as needed<br>• Keep your login credentials secure and confidential<br>• Notify us immediately of any unauthorized access to your account<br>• Accept responsibility for all activities under your account'],
            ['Partnership Terms', 'Platform access for facility operators is governed by individual Partnership Agreements that specify launch fees, ongoing platform fees, payment processing terms, and service scope. These Terms supplement but do not replace the specific provisions of your Partnership Agreement. In case of conflict, the Partnership Agreement shall prevail.'],
            ['Payment Terms', 'Partners agree to pay all fees as specified in their Partnership Agreement. Payment processing for facility transactions is handled through our integrated Square payment system. Standard processing rates apply as disclosed during onboarding. K2 reserves the right to modify fees with reasonable notice as specified in the Partnership Agreement.'],
            ['Acceptable Use', 'You agree not to:<br>• Use the platform for any unlawful purpose<br>• Attempt to gain unauthorized access to any part of the platform<br>• Interfere with or disrupt the platform or servers<br>• Transmit malicious code, viruses, or harmful content<br>• Scrape, crawl, or collect data without authorization<br>• Impersonate another person or entity<br>• Use the platform to send spam or unsolicited communications'],
            ['Intellectual Property', 'The K2 platform, including all software, design, content, logos, and trademarks, is owned by K2 and protected by intellectual property laws. Your partnership grants you a limited, non-exclusive, non-transferable license to use the platform for your facility operations. You may not copy, modify, distribute, or reverse engineer any part of the platform.'],
            ['Data Ownership', 'You retain ownership of your facility data, including customer records, transaction history, and operational data. K2 has a license to use this data as necessary to provide platform services and generate aggregated, anonymized analytics. Upon termination, you may request export of your data in standard formats.'],
            ['Service Availability', 'We strive for 99.9% platform uptime but do not guarantee uninterrupted access. Scheduled maintenance will be communicated in advance. We are not liable for downtime caused by factors beyond our reasonable control, including internet outages, natural disasters, or third-party service failures.'],
            ['Limitation of Liability', 'To the maximum extent permitted by law, K2 shall not be liable for indirect, incidental, special, consequential, or punitive damages arising from your use of the platform. Our total liability shall not exceed the amount of fees paid by you in the twelve (12) months preceding the claim.'],
            ['Indemnification', 'You agree to indemnify and hold harmless K2, its officers, directors, employees, and agents from any claims, damages, losses, or expenses (including reasonable attorney fees) arising from your use of the platform, violation of these Terms, or infringement of any third-party rights.'],
            ['Termination', 'Either party may terminate the relationship as specified in the Partnership Agreement. Upon termination, your access to the platform will be revoked. Provisions that by their nature should survive termination (including intellectual property, limitation of liability, and indemnification) shall survive.'],
            ['Governing Law', 'These Terms shall be governed by and construed in accordance with the laws of the State of Florida, without regard to conflict of law principles. Any disputes shall be resolved in the courts located in Hillsborough County, Florida.'],
            ['Changes to Terms', 'We may modify these Terms at any time. Material changes will be communicated to partners via email and platform notification. Continued use of the platform after changes take effect constitutes acceptance of the revised Terms.'],
            ['Contact Information', 'For questions regarding these Terms, contact us at:<br><br>K2 Pickleball Platform<br>Tampa Bay, Florida<br>Email: legal@k2pickleball.com'],
        ];
        foreach ($sections as $i => $sec): ?>
        <div class="<?= $i > 0 ? 'mt-10 pt-10 border-t border-navy-800/60' : '' ?>">
            <h2 class="font-display text-xl font-bold text-white mb-4"><?= ($i + 1) . '. ' . $sec[0] ?></h2>
            <div class="text-sm text-slate-400 leading-relaxed"><?= $sec[1] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
