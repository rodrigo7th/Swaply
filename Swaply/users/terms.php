<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Swaply</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'swaply-blue': '#3B82F6',
                        'swaply-green': '#10B981',
                        'swaply-light-blue': '#EFF6FF',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-swaply-blue to-swaply-green rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">S</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Swaply</span>
                    </a>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-swaply-blue transition-colors">Home</a>
                    <a href="login.php" class="text-gray-600 hover:text-swaply-blue transition-colors">Login</a>
                    <a href="register.php" class="text-swaply-blue font-medium">Register</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-blue-600 mb-4">Terms and Conditions</h1>
                <p class="text-gray-600 text-lg">Last updated: <?php echo date('F j, Y'); ?></p>
            </div>

            <div class="prose prose-lg max-w-none">
                <!-- Introduction -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">1. Introduction</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Welcome to Swaply, an online marketplace platform that facilitates the exchange of goods and services between users ("Platform"). These Terms and Conditions ("Terms") constitute a legally binding agreement between you ("User", "you", or "your") and Swaply ("we", "us", or "our") regarding your access to and use of our Platform.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        By accessing, browsing, or using the Swaply Platform, you acknowledge that you have read, understood, and agree to be bound by these Terms and all applicable laws and regulations. If you do not agree with any part of these Terms, you must not use our Platform. These Terms apply to all users of the Platform, including without limitation users who are browsers, vendors, customers, merchants, and contributors of content.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Swaply reserves the right to modify, update, or change these Terms at any time without prior notice. Your continued use of the Platform after any such changes constitutes your acceptance of the new Terms. It is your responsibility to review these Terms periodically for updates.
                    </p>
                </section>

                <!-- User Responsibilities -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">2. User Responsibilities</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        As a user of the Swaply Platform, you are solely responsible for your conduct and any content you submit, post, or display on the Platform. You agree to use the Platform only for lawful purposes and in accordance with these Terms. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        You warrant that all information provided during registration and throughout your use of the Platform is accurate, current, and complete. You agree to promptly update your account information to maintain its accuracy. You are responsible for ensuring that your use of the Platform complies with all applicable local, state, national, and international laws and regulations.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        When listing items for exchange, you represent and warrant that you have the legal right to offer such items, that the items are accurately described, and that you will fulfill your obligations in any completed exchanges. You are solely responsible for the condition, quality, safety, legality, and authenticity of items you list on the Platform.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        You agree to communicate respectfully with other users, respond promptly to inquiries about your listings, and conduct exchanges in good faith. You are responsible for arranging and completing the physical exchange of items, including determining meeting locations, transportation, and any associated costs.
                    </p>
                </section>

                <!-- Prohibited Activities -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">3. Prohibited Activities</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Users are strictly prohibited from engaging in the following activities on the Swaply Platform: listing illegal items, stolen goods, counterfeit products, or items that infringe on intellectual property rights; engaging in fraudulent activities, misrepresenting items, or providing false information; harassing, threatening, or abusing other users; attempting to circumvent the Platform's systems or security measures; using automated systems, bots, or scripts to access the Platform; and collecting user information for unauthorized purposes.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Additionally, users may not list items that are dangerous, hazardous, or require special handling; engage in money laundering, tax evasion, or other financial crimes; use the Platform for commercial purposes beyond personal item exchanges; create multiple accounts to manipulate the reputation system; or interfere with other users' ability to use and enjoy the Platform.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Violation of these prohibited activities may result in immediate suspension or termination of your account, removal of your listings, and potential legal action. Swaply reserves the right to investigate suspected violations and cooperate with law enforcement authorities as necessary.
                    </p>
                </section>

                <!-- Marketplace Disclaimer -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">4. Marketplace Disclaimer</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Swaply operates solely as an intermediary platform that facilitates connections between users who wish to exchange goods and services. We do not participate in, oversee, or control the actual exchanges between users. Swaply is not a party to any exchange agreement between users and does not take possession of, inspect, or verify the items being exchanged.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        We explicitly disclaim any responsibility for fraud, scams, misrepresentations, or disputes that may arise between users. Swaply does not guarantee the identity, legitimacy, or trustworthiness of any user, nor do we verify the accuracy of item descriptions, conditions, or availability. Users engage in exchanges entirely at their own risk and discretion.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        While we may provide tools such as user ratings and reviews to help users make informed decisions, these features are provided as-is and should not be considered as endorsements or guarantees. Users are strongly encouraged to exercise caution, meet in safe public locations, and thoroughly inspect items before completing any exchange.
                    </p>
                </section>

                <!-- Limitation of Liability -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">5. Limitation of Liability</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Swaply does not guarantee the quality, condition, safety, legality, or authenticity of items listed on the Platform. We make no warranties, express or implied, regarding the Platform's functionality, availability, or suitability for any particular purpose. The Platform is provided "as is" and "as available" without any warranties of any kind.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        To the maximum extent permitted by law, Swaply shall not be liable for any direct, indirect, incidental, special, consequential, or punitive damages arising from your use of the Platform, including but not limited to damages for loss of profits, goodwill, use, data, or other intangible losses, even if we have been advised of the possibility of such damages.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Swaply does not guarantee that exchanges will be completed successfully or that users will fulfill their obligations. We are not responsible for failed exchanges, damaged items, delivery issues, or any disputes between users. Our total liability to you for any claims arising from your use of the Platform shall not exceed the amount you have paid to us, if any, in the twelve months preceding the claim.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Some jurisdictions do not allow the exclusion or limitation of certain warranties or liabilities, so some of the above limitations may not apply to you. In such cases, our liability will be limited to the fullest extent permitted by applicable law.
                    </p>
                </section>

                <!-- Privacy & Data -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">6. Privacy & Data Protection</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Swaply is committed to protecting your privacy and personal data in accordance with applicable data protection laws. Our Privacy Policy, which is incorporated into these Terms by reference, details how we collect, use, store, and protect your personal information. By using the Platform, you consent to the collection and use of your information as described in our Privacy Policy.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        You are solely responsible for the information you choose to share with other users through the Platform. This includes item descriptions, photos, messages, and any personal information disclosed during exchanges. Swaply strongly advises users to exercise caution when sharing personal information and to avoid sharing sensitive data such as financial information, home addresses, or identification documents unless absolutely necessary for the exchange.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        While we implement reasonable security measures to protect user data, you acknowledge that no system is completely secure. You agree to immediately notify us of any unauthorized access to your account or any other security breach. You are responsible for maintaining the security of your account credentials and for all activities that occur under your account.
                    </p>
                </section>

                <!-- Changes to Terms -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">7. Changes to Terms</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Swaply reserves the right to modify, update, or revise these Terms and Conditions at any time, at our sole discretion, without prior notice to users. Changes may be made to reflect changes in our services, legal requirements, business practices, or for any other reason we deem necessary. When we make changes to these Terms, we will update the "Last updated" date at the top of this document.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Your continued use of the Platform after any changes to these Terms constitutes your acceptance of the revised Terms. If you do not agree with any changes, you must discontinue your use of the Platform immediately. It is your responsibility to review these Terms periodically to stay informed of any updates or modifications.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        For significant changes that materially affect your rights or obligations, we may, at our discretion, provide additional notice through the Platform, email, or other communication methods. However, we are not obligated to provide such notice, and the absence of notice does not invalidate any changes to these Terms.
                    </p>
                </section>

                <!-- Governing Law -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">8. Governing Law & Jurisdiction</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        These Terms and Conditions shall be governed by and construed in accordance with the laws of El Salvador, without regard to its conflict of law provisions. Any disputes arising from or relating to these Terms or your use of the Platform shall be subject to the exclusive jurisdiction of the courts of El Salvador.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        You agree to submit to the personal jurisdiction of the courts located in El Salvador for the purpose of litigating all such claims or disputes. If any provision of these Terms is found to be unenforceable or invalid by a court of competent jurisdiction, the remaining provisions shall remain in full force and effect.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Before initiating any legal proceedings, parties agree to attempt to resolve disputes through good faith negotiations. If such negotiations fail, disputes may be resolved through binding arbitration in accordance with the rules of the applicable arbitration association in El Salvador, unless prohibited by local law.
                    </p>
                </section>

                <!-- Contact Information -->
                <section class="mb-10">
                    <h2 class="text-2xl font-semibold text-blue-600 mb-4">9. Contact Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you have any questions, concerns, or complaints regarding these Terms and Conditions or your use of the Swaply Platform, please contact us using the following information:
                    </p>
                    <div class="bg-gray-50 p-6 rounded-lg mb-4">
                        <p class="text-gray-700 leading-relaxed mb-2"><strong>Email:</strong> legal@swaply.com</p>
                        <p class="text-gray-700 leading-relaxed mb-2"><strong>Phone:</strong> +503 2XXX-XXXX</p>
                        <p class="text-gray-700 leading-relaxed mb-2"><strong>Address:</strong> San Salvador, El Salvador</p>
                        <p class="text-gray-700 leading-relaxed"><strong>Business Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM (CST)</p>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        We strive to respond to all inquiries within 48 hours during business days. For urgent matters related to account security or platform abuse, please mark your communication as "URGENT" in the subject line. Please note that contacting us does not create an attorney-client relationship, and any information shared should not include confidential or sensitive personal data unless specifically requested for account verification purposes.
                    </p>
                </section>
            </div>

            <!-- Back to Register Button -->
            <div class="text-center mt-12 pt-8 border-t border-gray-200">
                <a href="register.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-swaply-blue to-swaply-green text-white font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Register
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <div class="w-6 h-6 bg-gradient-to-r from-swaply-blue to-swaply-green rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xs">S</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900">Swaply</span>
                </div>
                <p class="text-gray-600 text-sm">© <?php echo date('Y'); ?> Swaply. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
