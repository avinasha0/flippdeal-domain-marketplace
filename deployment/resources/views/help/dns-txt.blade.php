@extends('layouts.app')

@section('title', 'How to Add DNS TXT Record - Domain Verification Help')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                How to Add DNS TXT Record
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Step-by-step instructions for verifying domain ownership
            </p>
        </div>

        <!-- Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">What is DNS TXT Verification?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                DNS TXT verification is a secure method to prove you own a domain by adding a specific text record to your domain's DNS settings. This is the most reliable way to verify domain ownership.
            </p>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-blue-800 dark:text-blue-200 text-sm">
                    <strong>Why use DNS TXT?</strong> It's secure, doesn't require file uploads, and works for any domain type. The verification token is unique to your domain and expires after 2 hours for security.
                </p>
            </div>
        </div>

        <!-- General Steps -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">General Steps</h2>
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Log into your domain registrar or DNS provider</h3>
                        <p class="text-gray-600 dark:text-gray-400">Access your domain management panel where you can modify DNS records.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">2</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Navigate to DNS management</h3>
                        <p class="text-gray-600 dark:text-gray-400">Look for sections like "DNS Management", "DNS Records", or "Zone Editor".</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">3</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Add a new TXT record</h3>
                        <p class="text-gray-600 dark:text-gray-400">Create a new DNS record with the following details:</p>
                        <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <li>• <strong>Type:</strong> TXT</li>
                            <li>• <strong>Name/Host:</strong> @ or your domain name</li>
                            <li>• <strong>Value:</strong> [Your unique verification token]</li>
                            <li>• <strong>TTL:</strong> 300 seconds (5 minutes)</li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">4</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Save and wait for propagation</h3>
                        <p class="text-gray-600 dark:text-gray-400">DNS changes can take 5-60 minutes to propagate worldwide. Our system will automatically check for the record.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrar-Specific Instructions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Registrar-Specific Instructions</h2>
            
            <div class="space-y-8">
                <!-- GoDaddy -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <img src="https://img.icons8.com/color/24/000000/godaddy.png" alt="GoDaddy" class="mr-3">
                        GoDaddy
                    </h3>
                    <ol class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>1. Log into your GoDaddy account</li>
                        <li>2. Go to "My Products" → "All Products and Services"</li>
                        <li>3. Find your domain and click "DNS"</li>
                        <li>4. Click "Add" to create a new record</li>
                        <li>5. Select "TXT" as the record type</li>
                        <li>6. Enter "@" in the Name field</li>
                        <li>7. Paste your verification token in the Value field</li>
                        <li>8. Set TTL to 600 seconds</li>
                        <li>9. Click "Save"</li>
                    </ol>
                </div>

                <!-- Namecheap -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <img src="https://img.icons8.com/color/24/000000/namecheap.png" alt="Namecheap" class="mr-3">
                        Namecheap
                    </h3>
                    <ol class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>1. Log into your Namecheap account</li>
                        <li>2. Go to "Domain List" and click "Manage" next to your domain</li>
                        <li>3. Click on the "Advanced DNS" tab</li>
                        <li>4. Click "Add New Record"</li>
                        <li>5. Select "TXT Record" as the type</li>
                        <li>6. Enter "@" in the Host field</li>
                        <li>7. Paste your verification token in the Value field</li>
                        <li>8. Set TTL to 300 seconds</li>
                        <li>9. Click the checkmark to save</li>
                    </ol>
                </div>

                <!-- Cloudflare -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <img src="https://img.icons8.com/color/24/000000/cloudflare.png" alt="Cloudflare" class="mr-3">
                        Cloudflare
                    </h3>
                    <ol class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>1. Log into your Cloudflare dashboard</li>
                        <li>2. Select your domain from the list</li>
                        <li>3. Go to the "DNS" tab</li>
                        <li>4. Click "Add record"</li>
                        <li>5. Select "TXT" as the record type</li>
                        <li>6. Enter "@" in the Name field</li>
                        <li>7. Paste your verification token in the Content field</li>
                        <li>8. Set TTL to "Auto" or 300 seconds</li>
                        <li>9. Click "Save"</li>
                    </ol>
                </div>

                <!-- Google Domains -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <img src="https://img.icons8.com/color/24/000000/google-domains.png" alt="Google Domains" class="mr-3">
                        Google Domains
                    </h3>
                    <ol class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>1. Log into your Google Domains account</li>
                        <li>2. Click on your domain name</li>
                        <li>3. Go to the "DNS" tab</li>
                        <li>4. Scroll down to "Custom resource records"</li>
                        <li>5. Click "Add custom record"</li>
                        <li>6. Select "TXT" as the record type</li>
                        <li>7. Enter "@" in the Name field</li>
                        <li>8. Paste your verification token in the Data field</li>
                        <li>9. Set TTL to 300 seconds</li>
                        <li>10. Click "Add"</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Troubleshooting</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Common Issues</h3>
                    <div class="space-y-4">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">"TXT record not found"</h4>
                            <p class="text-yellow-700 dark:text-yellow-300 text-sm">
                                Make sure you've saved the DNS record and waited at least 5 minutes for propagation. 
                                Double-check that the record type is "TXT" and the value matches exactly.
                            </p>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">"Token expired"</h4>
                            <p class="text-yellow-700 dark:text-yellow-300 text-sm">
                                Verification tokens expire after 2 hours for security. Click "Retry Verification" 
                                to generate a new token and try again.
                            </p>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">"DNS propagation delay"</h4>
                            <p class="text-yellow-700 dark:text-yellow-300 text-sm">
                                DNS changes can take up to 24 hours to propagate globally. If you've added the record 
                                correctly, please wait and try again later.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Verification Tips</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• Copy the verification token exactly as shown (no extra spaces or characters)</li>
                        <li>• Use "@" as the hostname for the root domain</li>
                        <li>• Set TTL to 300 seconds (5 minutes) for faster propagation</li>
                        <li>• Wait 5-10 minutes after adding the record before checking verification</li>
                        <li>• If using a subdomain, enter the subdomain name instead of "@"</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Support -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-semibold text-blue-900 dark:text-blue-100 mb-4">Need Help?</h2>
            <p class="text-blue-800 dark:text-blue-200 mb-6">
                If you're still having trouble with domain verification, our support team is here to help.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('support.contact', ['subject' => 'Domain Verification Help']) }}" 
                   class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Contact Support
                </a>
                <a href="{{ route('help.index') }}" 
                   class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                    Help Center
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
