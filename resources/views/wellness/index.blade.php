@extends('layouts.app')

@section('title', 'Wellness Recommendations')

@section('content')
<div class="min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2"> Wellness Recommendations</h1>
            <p class="text-gray-600">Get personalized insights based on your mood trends and habit progress</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <!-- Generate Button -->
            <div id="generateSection" class="text-center">
                <div class="mb-6">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-5xl">💭</span>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Ready for Your Weekly Reflection?</h2>
                    <p class="text-gray-600 mb-6">Click the button below to generate personalized wellness recommendations based on your recent mood trends and habit completion data.</p>
                </div>
                
                <button id="generateBtn" 
                        class="px-8 py-4 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-semibold rounded-xl shadow-lg hover:from-amber-500 hover:to-orange-600 transform hover:scale-105 transition-all duration-200 text-lg">
                    ✨ Generate Recommendations
                </button>
            </div>

            <!-- Loading State -->
            <div id="loadingSection" class="hidden text-center py-12">
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-amber-400 border-t-transparent mb-4"></div>
                <p class="text-gray-600 text-lg">Generating your personalized wellness recommendations...</p>
                <p class="text-gray-500 text-sm mt-2">This may take a few moments</p>
            </div>

            <!-- Recommendation Display -->
            <div id="recommendationSection" class="hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-3xl">💫</span>
                        Your Weekly Reflection
                    </h2>
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-200">
                        <div id="recommendationText" class="text-gray-700 leading-relaxed text-lg whitespace-pre-line"></div>
                    </div>
                </div>

                <!-- Data Summary (Collapsible) -->
                <div class="mt-6">
                    <button id="toggleDataBtn" 
                            class="text-sm text-gray-600 hover:text-gray-800 flex items-center gap-2 mb-3">
                        <span>📊</span>
                        <span>View data used for this recommendation</span>
                        <span id="toggleIcon" class="transform transition-transform">▼</span>
                    </button>
                    <div id="dataSummary" class="hidden bg-gray-50 rounded-xl p-4 text-sm text-gray-600 space-y-2">
                        <div><strong>Period:</strong> <span id="periodRange"></span></div>
                        <div><strong>Mood Trend:</strong> <span id="moodTrend"></span></div>
                        <div><strong>Habit Summary:</strong> <span id="habitSummary"></span></div>
                    </div>
                </div>

                <!-- Regenerate Button -->
                <div class="mt-6 text-center">
                    <button id="regenerateBtn" 
                            class="px-6 py-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white font-medium rounded-xl shadow-md hover:from-amber-500 hover:to-orange-600 transform hover:scale-105 transition-all duration-200">
                        🔄 Generate New Recommendations
                    </button>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorSection" class="hidden text-center py-8">
                <div class="text-red-500 text-5xl mb-4">⚠️</div>
                <p id="errorMessage" class="text-red-600 text-lg mb-4"></p>
                <button id="retryBtn" 
                        class="px-6 py-3 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition-colors">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateBtn');
    const regenerateBtn = document.getElementById('regenerateBtn');
    const retryBtn = document.getElementById('retryBtn');
    const toggleDataBtn = document.getElementById('toggleDataBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const dataSummary = document.getElementById('dataSummary');
    
    const generateSection = document.getElementById('generateSection');
    const loadingSection = document.getElementById('loadingSection');
    const recommendationSection = document.getElementById('recommendationSection');
    const errorSection = document.getElementById('errorSection');

    function showLoading() {
        generateSection.classList.add('hidden');
        recommendationSection.classList.add('hidden');
        errorSection.classList.add('hidden');
        loadingSection.classList.remove('hidden');
    }

    function showRecommendation(data) {
        loadingSection.classList.add('hidden');
        errorSection.classList.add('hidden');
        generateSection.classList.add('hidden');
        recommendationSection.classList.remove('hidden');
        
        document.getElementById('recommendationText').textContent = data.recommendation;
        
        if (data.data) {
            const periodStart = data.data.period_start || '';
            const periodEnd = data.data.period_end || '';
            if (periodStart && periodEnd) {
                document.getElementById('periodRange').textContent = `${periodStart} to ${periodEnd}`;
            } else {
                document.getElementById('periodRange').textContent = 'Last 7 days (or available data)';
            }
            document.getElementById('moodTrend').textContent = data.data.mood_trend || 'N/A';
            document.getElementById('habitSummary').textContent = data.data.habit_summary || 'N/A';
        }
    }

    function showError(message) {
        loadingSection.classList.add('hidden');
        recommendationSection.classList.add('hidden');
        generateSection.classList.add('hidden');
        errorSection.classList.remove('hidden');
        document.getElementById('errorMessage').textContent = message;
    }

    function generateRecommendation() {
        showLoading();

        fetch('{{ route("wellness.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showRecommendation(data);
            } else {
                showError(data.message || 'Failed to generate recommendations. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred. Please try again later.');
        });
    }

    generateBtn.addEventListener('click', generateRecommendation);
    regenerateBtn.addEventListener('click', generateRecommendation);
    retryBtn.addEventListener('click', generateRecommendation);

    toggleDataBtn.addEventListener('click', function() {
        const isHidden = dataSummary.classList.contains('hidden');
        dataSummary.classList.toggle('hidden');
        toggleIcon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    });
});
</script>
@endsection
