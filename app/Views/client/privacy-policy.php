<?php $pageTitle = 'Privacy Policy — K2 Pickleball Platform'; ?>

<section class="relative pt-32 pb-20 hero-glow overflow-hidden">
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
        <h1 class="font-display text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight animate-fade-in-up">
            Privacy <span class="gradient-gold">Policy</span>
        </h1>
        <p class="mt-4 text-sm text-slate-400 animate-fade-in-up" style="animation-delay:0.1s">Last updated: <?= date('F j, Y') ?></p>
    </div>
</section>

<section class="py-16 lg:py-24 relative">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 prose-custom">
        <?php
        $sections = [
            ['Introduction', 'K2 Pickleball Platform ("K2", "we", "us", or "our") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website, use our platform, or engage with our services. Please read this policy carefully. By using our services, you consent to the practices described herein.'],
            ['Information We Collect', '<strong class="text-white">Personal Information:</strong> Name, email address, phone number, billing address, and payment information provided during registration, partnership applications, or contact form submissions.<br><br><strong class="text-white">Usage Data:</strong> We automatically collect information about how you interact with our platform, including IP address, browser type, pages visited, time spent on pages, and referring URLs.<br><br><strong class="text-white">Facility Data:</strong> For partner facilities, we collect operational data including court reservations, membership records, transaction history, and player profiles as necessary to provide our platform services.'],
            ['How We Use Your Information', 'We use collected information to:<br>• Provide, maintain, and improve our platform services<br>• Process partnership applications and manage partner accounts<br>• Process payments and send transaction confirmations<br>• Send administrative communications, updates, and security alerts<br>• Respond to inquiries, support requests, and feedback<br>• Generate aggregated analytics and performance insights for partners<br>• Comply with legal obligations and enforce our terms'],
            ['Data Sharing & Disclosure', 'We do not sell your personal information. We may share data with:<br>• <strong class="text-white">Payment processors</strong> (Square) to facilitate transactions<br>• <strong class="text-white">Cloud hosting providers</strong> for infrastructure and data storage<br>• <strong class="text-white">Partner facilities</strong> — player and customer data is shared with the specific facility the customer interacts with<br>• <strong class="text-white">Legal authorities</strong> when required by law or to protect our rights'],
            ['Data Security', 'We implement industry-standard security measures including encryption in transit (TLS/SSL), encryption at rest, secure authentication (JWT), role-based access controls, and regular security audits. While we strive to protect your data, no method of transmission or storage is 100% secure.'],
            ['Data Retention', 'We retain personal information for as long as your account is active or as needed to provide services. Partnership and transaction data is retained in accordance with applicable financial record-keeping requirements. You may request deletion of your personal data by contacting us.'],
            ['Your Rights', 'Depending on your jurisdiction, you may have the right to:<br>• Access the personal information we hold about you<br>• Request correction of inaccurate information<br>• Request deletion of your personal data<br>• Object to or restrict certain processing activities<br>• Data portability<br><br>To exercise these rights, contact us at info@k2pickleball.com.'],
            ['Cookies & Tracking', 'We use essential cookies to maintain session state and platform functionality. We may use analytics cookies to understand usage patterns and improve our services. You can control cookie preferences through your browser settings.'],
            ['Third-Party Links', 'Our platform may contain links to third-party websites or services. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies.'],
            ['Changes to This Policy', 'We may update this Privacy Policy from time to time. We will notify you of material changes by posting the updated policy on our website and updating the "Last updated" date. Continued use of our services after changes constitutes acceptance of the revised policy.'],
            ['Contact Us', 'If you have questions about this Privacy Policy or our data practices, please contact us at:<br><br>K2 Pickleball Platform<br>Tampa Bay, Florida<br>Email: info@k2pickleball.com'],
        ];
        foreach ($sections as $i => $sec): ?>
        <div class="<?= $i > 0 ? 'mt-10 pt-10 border-t border-navy-800/60' : '' ?>">
            <h2 class="font-display text-xl font-bold text-white mb-4"><?= ($i + 1) . '. ' . $sec[0] ?></h2>
            <div class="text-sm text-slate-400 leading-relaxed"><?= $sec[1] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
