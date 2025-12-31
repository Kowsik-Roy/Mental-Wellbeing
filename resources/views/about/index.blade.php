@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-indigo-900 mb-4">About Mental Wellbeing</h1>
        <p class="text-xl text-gray-700">Your journey to better mental health starts here</p>
    </div>

    <!-- Vision Section -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-indigo-900 mb-6 pb-3 border-b-2 border-indigo-200">
            <i class="fas fa-eye text-indigo-600 mr-3"></i>Our Vision
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed mb-4">
            To create a peaceful, supportive digital space where individuals can nurture their mental wellness through 
            mindful habits, reflective journaling, and personalized insights. We believe that mental health is not a 
            destination but a continuous journey of self-discovery and growth.
        </p>
        <p class="text-gray-700 text-lg leading-relaxed">
            Our vision is to empower every user to build sustainable wellness routines, understand their emotional patterns, 
            and celebrate their progress—no matter how small. We envision a world where mental wellness tools are accessible, 
            non-judgmental, and genuinely supportive of each person's unique journey.
        </p>
    </div>

    <!-- Mission Section -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-indigo-900 mb-6 pb-3 border-b-2 border-pink-200">
            <i class="fas fa-heart text-pink-600 mr-3"></i>Our Mission
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed mb-4">
            Mental Wellbeing was created to bridge the gap between recognizing the importance of mental health 
            and actually taking consistent, meaningful action. We understand that maintaining mental wellness requires more 
            than just awareness—it requires tools, structure, and gentle encouragement.
        </p>
        <p class="text-gray-700 text-lg leading-relaxed mb-4">
            Our mission is to provide a comprehensive yet simple platform that helps users:
        </p>
        <ul class="list-disc list-inside text-gray-700 text-lg leading-relaxed space-y-2 ml-4">
            <li>Build and maintain healthy habits that support their mental wellness</li>
            <li>Express and process their emotions through guided journaling</li>
            <li>Track their progress and celebrate their achievements</li>
            <li>Receive personalized insights and encouragement powered by AI</li>
            <li>Feel supported without judgment or pressure</li>
        </ul>
    </div>

    <!-- AI Features Section -->
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl p-8 md:p-12 shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-indigo-900 mb-6 pb-3 border-b-2 border-indigo-300">
            <i class="fas fa-robot text-indigo-600 mr-3"></i>AI-Powered Features
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed mb-6">
            We leverage advanced AI technology to provide you with personalized, supportive insights that enhance your wellness journey. 
            Our AI features are designed to be helpful, empathetic, and non-judgmental—never replacing professional care, but 
            complementing your self-care practices.
        </p>

        <div class="space-y-6">
            <!-- Emotional Reflection -->
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-comments text-purple-600 mr-3"></i>
                    Emotional Reflection for Journal Entries
                </h3>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Every time you write a journal entry and select your mood, our AI provides an immediate, supportive reflection 
                    that acknowledges your feelings. This feature offers gentle emotional validation without analysis or advice—just 
                    a warm, understanding response to help you feel heard and supported.
                </p>
                <p class="text-gray-600 text-sm italic">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    The AI reads your journal entry and mood selection to generate a personalized, empathetic response that validates your emotional experience.
                </p>
            </div>

            <!-- Wellness Recommendations -->
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-amber-600 mr-3"></i>
                    Personalized Wellness Recommendations
                </h3>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Based on your mood trends and habit completion data over the past week, our AI generates personalized wellness 
                    recommendations. These insights highlight your progress, acknowledge your strengths, and suggest small, realistic 
                    improvements—all delivered in a supportive, encouraging tone.
                </p>
                <p class="text-gray-600 text-sm italic">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    The AI analyzes your weekly patterns to provide tailored encouragement and gentle guidance for your wellness journey.
                </p>
            </div>

            <!-- Daily Inspirational Quotes -->
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-quote-left text-green-600 mr-3"></i>
                    Daily Inspirational Quotes
                </h3>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Each day, you receive a unique inspirational quote tailored to your account. These quotes refresh daily and are 
                    sourced from Zenquotes API, providing you with fresh motivation and perspective every day.
                </p>
                <p class="text-gray-600 text-sm italic">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    Quotes are personalized per user and cached for 12 hours to ensure you get a new, meaningful quote each day.
                </p>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-xl border-l-4 border-blue-500">
            <p class="text-gray-700 text-sm">
                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                <strong>Privacy & AI:</strong> All AI processing respects your privacy. Your journal entries and data are processed securely, 
                and we never share your personal information. The AI is designed to be supportive and non-judgmental, focusing on 
                encouragement rather than analysis or diagnosis.
            </p>
        </div>
    </div>

    <!-- Why We Built This Section -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-indigo-900 mb-6 pb-3 border-b-2 border-green-200">
            <i class="fas fa-lightbulb text-green-600 mr-3"></i>Why We Built This
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed mb-4">
            Mental health challenges affect millions of people worldwide, yet many struggle to find accessible, 
            non-intimidating tools to support their wellness journey. Traditional mental health resources can feel 
            clinical, overwhelming, or disconnected from daily life.
        </p>
        <p class="text-gray-700 text-lg leading-relaxed mb-4">
            We built Mental Wellbeing because we believe that:
        </p>
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-green-500">
                <p class="font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Wellness Should Be Accessible
                </p>
                <p class="text-gray-600 text-sm">Everyone deserves tools to support their mental health, regardless of their circumstances.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-blue-500">
                <p class="font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                    Small Steps Create Big Change
                </p>
                <p class="text-gray-600 text-sm">Consistent, small actions are more powerful than occasional grand gestures.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-purple-500">
                <p class="font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                    Progress Deserves Recognition
                </p>
                <p class="text-gray-600 text-sm">Every step forward, no matter how small, is worth celebrating.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-indigo-500">
                <p class="font-semibold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                    Privacy and Safety Matter
                </p>
                <p class="text-gray-600 text-sm">Your mental health journey is personal, and your data should be protected.</p>
            </div>
        </div>
        <p class="text-gray-700 text-lg leading-relaxed">
            Mental Wellbeing is our contribution to making mental health support more approachable, 
            practical, and integrated into everyday life. We're here to walk alongside you on your journey, 
            providing the tools and encouragement you need to thrive.
        </p>
    </div>

    <!-- Values Section -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-lg mb-8">
        <h2 class="text-3xl font-bold text-indigo-900 mb-6 pb-3 border-b-2 border-amber-200">
            <i class="fas fa-star text-amber-600 mr-3"></i>Our Core Values
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-indigo-50 rounded-xl">
                <i class="fas fa-hands-helping text-indigo-600 text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg text-gray-900 mb-2">Compassion</h3>
                <p class="text-gray-600 text-sm">We approach mental wellness with kindness, understanding, and zero judgment.</p>
            </div>
            <div class="text-center p-6 bg-pink-50 rounded-xl">
                <i class="fas fa-shield-alt text-pink-600 text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg text-gray-900 mb-2">Privacy</h3>
                <p class="text-gray-600 text-sm">Your data is yours. We protect your privacy and keep your information secure.</p>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-xl">
                <i class="fas fa-seedling text-green-600 text-3xl mb-3"></i>
                <h3 class="font-semibold text-lg text-gray-900 mb-2">Growth</h3>
                <p class="text-gray-600 text-sm">We believe in continuous improvement, both for our platform and for our users.</p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    @auth
    <div class="text-center mb-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    @else
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>
    </div>
    @endauth
</div>

@endsection
