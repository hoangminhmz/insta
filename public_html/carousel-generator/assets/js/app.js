/**
 * Instagram Carousel Generator - Main Application Logic
 */

// Global state
const AppState = {
    currentStep: 1,
    carouselData: null,
    designSettings: {
        primaryColor: '#000000',
        accentColor: '#FF6B9D',
        backgroundColor: '#FFFFFF',
        headingFont: 'Poppins',
        bodyFont: 'Inter',
        padding: 80,
        textSize: 0,
        alignment: 'left'
    },
    username: 'yourusername',
    currentSlideIndex: 0
};

// DOM Elements
const elements = {
    // Step 1
    contentForm: document.getElementById('contentForm'),
    blogContent: document.getElementById('blogContent'),
    charCount: document.getElementById('charCount'),
    nicheSelector: document.getElementById('nicheSelector'),
    toneSelector: document.getElementById('toneSelector'),
    username: document.getElementById('username'),
    analyzeBtn: document.getElementById('analyzeBtn'),

    // Step 2
    slidesPreview: document.getElementById('slidesPreview'),
    regenerateAllBtn: document.getElementById('regenerateAllBtn'),
    backToStep1: document.getElementById('backToStep1'),
    proceedToDesign: document.getElementById('proceedToDesign'),

    // Step 3
    previewCanvas: document.getElementById('previewCanvas'),
    primaryColor: document.getElementById('primaryColor'),
    accentColor: document.getElementById('accentColor'),
    backgroundColor: document.getElementById('backgroundColor'),
    headingFont: document.getElementById('headingFont'),
    bodyFont: document.getElementById('bodyFont'),
    paddingSlider: document.getElementById('paddingSlider'),
    paddingValue: document.getElementById('paddingValue'),
    textSizeSlider: document.getElementById('textSizeSlider'),
    textSizeValue: document.getElementById('textSizeValue'),
    textAlignment: document.getElementById('textAlignment'),
    applyToAll: document.getElementById('applyToAll'),
    resetDesign: document.getElementById('resetDesign'),
    prevSlide: document.getElementById('prevSlide'),
    nextSlide: document.getElementById('nextSlide'),
    currentSlideNum: document.getElementById('currentSlideNum'),
    backToStep2: document.getElementById('backToStep2'),
    generateImages: document.getElementById('generateImages'),

    // Step 4
    loadingSpinner: document.getElementById('loadingSpinner'),
    downloadSection: document.getElementById('downloadSection'),
    progressBar: document.getElementById('progressBar'),
    downloadZipBtn: document.getElementById('downloadZipBtn'),
    generateAgain: document.getElementById('generateAgain'),

    // Steps
    steps: document.querySelectorAll('.step'),
    stepContents: document.querySelectorAll('.step-content')
};

/**
 * Initialize Application
 */
document.addEventListener('DOMContentLoaded', () => {
    initializeEventListeners();
    updateCharCount();
});

/**
 * Set up all event listeners
 */
function initializeEventListeners() {
    // Step 1: Content input
    elements.blogContent.addEventListener('input', updateCharCount);
    elements.contentForm.addEventListener('submit', handleContentSubmit);

    // Step 2: Review slides
    elements.backToStep1.addEventListener('click', () => navigateToStep(1));
    elements.proceedToDesign.addEventListener('click', () => navigateToStep(3));
    elements.regenerateAllBtn.addEventListener('click', handleRegenerateAll);

    // Step 3: Design customization
    elements.primaryColor.addEventListener('change', handleDesignChange);
    elements.accentColor.addEventListener('change', handleDesignChange);
    elements.backgroundColor.addEventListener('change', handleDesignChange);
    elements.headingFont.addEventListener('change', handleDesignChange);
    elements.bodyFont.addEventListener('change', handleDesignChange);
    elements.paddingSlider.addEventListener('input', handlePaddingChange);
    elements.textSizeSlider.addEventListener('input', handleTextSizeChange);
    elements.textAlignment.addEventListener('change', handleDesignChange);
    elements.resetDesign.addEventListener('click', resetDesignSettings);
    elements.prevSlide.addEventListener('click', showPreviousSlide);
    elements.nextSlide.addEventListener('click', showNextSlide);
    elements.backToStep2.addEventListener('click', () => navigateToStep(2));
    elements.generateImages.addEventListener('click', handleGenerateImages);

    // Step 4: Download
    elements.generateAgain.addEventListener('click', () => {
        location.reload();
    });
}

/**
 * Update character count
 */
function updateCharCount() {
    const count = elements.blogContent.value.length;
    elements.charCount.textContent = count;

    if (count > 10000) {
        elements.blogContent.value = elements.blogContent.value.substring(0, 10000);
        elements.charCount.textContent = '10000';
    }
}

/**
 * Handle content form submission
 */
async function handleContentSubmit(e) {
    e.preventDefault();

    const content = elements.blogContent.value.trim();
    const niche = elements.nicheSelector.value;
    const tone = elements.toneSelector.value;
    const username = elements.username.value.trim() || 'yourusername';

    // Validation
    if (!content || content.length < 100) {
        showAlert('Please enter at least 100 characters of content.', 'warning');
        return;
    }

    if (!niche || !tone) {
        showAlert('Please select both niche and tone.', 'warning');
        return;
    }

    // Store username
    AppState.username = username;

    // Show loading state
    elements.analyzeBtn.disabled = true;
    elements.analyzeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analyzing...';

    try {
        // Call API
        const response = await fetch('generate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                content,
                niche,
                tone,
                username
            })
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'API request failed');
        }

        // Store carousel data
        AppState.carouselData = result.data;

        // Display slides for review
        displaySlidesPreview();

        // Navigate to step 2
        navigateToStep(2);

    } catch (error) {
        console.error('Error:', error);
        showAlert('Failed to generate carousel: ' + error.message, 'danger');
    } finally {
        // Reset button
        elements.analyzeBtn.disabled = false;
        elements.analyzeBtn.innerHTML = '<i class="bi bi-magic me-2"></i>Analyze Content with AI';
    }
}

/**
 * Display slides preview in Step 2
 */
function displaySlidesPreview() {
    if (!AppState.carouselData) return;

    elements.slidesPreview.innerHTML = '';

    Object.keys(AppState.carouselData).forEach((slideKey, index) => {
        const slideData = AppState.carouselData[slideKey];
        const slideNum = index + 1;

        const slideCard = createSlidePreviewCard(slideKey, slideData, slideNum);
        elements.slidesPreview.appendChild(slideCard);
    });
}

/**
 * Create slide preview card
 */
function createSlidePreviewCard(slideKey, slideData, slideNum) {
    const card = document.createElement('div');
    card.className = 'slide-preview-card';
    card.innerHTML = `
        <div class="slide-preview-header">
            <span class="badge bg-primary">Slide ${slideNum}</span>
            <span class="badge bg-secondary">${slideData.type}</span>
        </div>
        <div class="slide-preview-content">
            ${renderSlidePreviewContent(slideData)}
        </div>
        <div class="slide-preview-footer">
            <button class="btn btn-sm btn-outline-primary edit-slide" data-slide="${slideKey}">
                <i class="bi bi-pencil"></i> Edit
            </button>
        </div>
    `;

    // Add edit functionality
    card.querySelector('.edit-slide').addEventListener('click', () => {
        editSlide(slideKey, slideData);
    });

    return card;
}

/**
 * Render slide preview content based on type
 */
function renderSlidePreviewContent(slideData) {
    switch (slideData.type) {
        case 'hook':
            return `
                <p class="fw-bold">${slideData.text} ${slideData.emoji || ''}</p>
                <small class="text-muted">Keywords: ${slideData.keywords?.join(', ') || 'None'}</small>
            `;

        case 'content':
            return `
                <h6 class="fw-bold">${slideData.heading}</h6>
                <p>${slideData.body}</p>
                <small class="text-muted">Keywords: ${slideData.keywords?.join(', ') || 'None'}</small>
            `;

        case 'list':
            return `
                <h6 class="fw-bold">${slideData.heading}</h6>
                <ul class="small">
                    ${slideData.items?.map(item => `<li>${item}</li>`).join('') || ''}
                </ul>
            `;

        case 'bonus':
            return `
                <h6 class="fw-bold">${slideData.heading}</h6>
                <p>${slideData.body}</p>
            `;

        case 'cta':
            return `
                <p class="fw-bold">${slideData.text}</p>
                <p class="text-muted mb-0">${slideData.username}</p>
            `;

        default:
            return '<p>Unknown slide type</p>';
    }
}

/**
 * Edit slide (inline editing)
 */
function editSlide(slideKey, slideData) {
    // Simple implementation - can be enhanced with modal
    const newText = prompt('Edit slide text:', slideData.text || slideData.body || '');
    if (newText !== null) {
        if (slideData.text) slideData.text = newText;
        if (slideData.body) slideData.body = newText;
        displaySlidesPreview();
    }
}

/**
 * Handle regenerate all
 */
function handleRegenerateAll() {
    if (confirm('This will regenerate all slides. Are you sure?')) {
        navigateToStep(1);
    }
}

/**
 * Navigate to specific step
 */
function navigateToStep(stepNum) {
    AppState.currentStep = stepNum;

    // Update step indicators
    elements.steps.forEach((step, index) => {
        if (index + 1 <= stepNum) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });

    // Show/hide step contents
    elements.stepContents.forEach((content, index) => {
        if (index + 1 === stepNum) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });

    // Initialize step-specific functionality
    if (stepNum === 3) {
        initializeCanvasPreview();
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Initialize canvas preview in Step 3
 */
function initializeCanvasPreview() {
    if (typeof CanvasGenerator !== 'undefined') {
        CanvasGenerator.init(elements.previewCanvas, AppState);
        renderCurrentSlide();
    }
}

/**
 * Handle design changes
 */
function handleDesignChange(e) {
    const setting = e.target.id;
    const value = e.target.value;

    switch (setting) {
        case 'primaryColor':
            AppState.designSettings.primaryColor = value;
            break;
        case 'accentColor':
            AppState.designSettings.accentColor = value;
            break;
        case 'backgroundColor':
            AppState.designSettings.backgroundColor = value;
            break;
        case 'headingFont':
            AppState.designSettings.headingFont = value;
            break;
        case 'bodyFont':
            AppState.designSettings.bodyFont = value;
            break;
        case 'textAlignment':
            AppState.designSettings.alignment = value;
            break;
    }

    renderCurrentSlide();
}

/**
 * Handle padding change
 */
function handlePaddingChange(e) {
    const value = parseInt(e.target.value);
    AppState.designSettings.padding = value;
    elements.paddingValue.textContent = value;
    renderCurrentSlide();
}

/**
 * Handle text size change
 */
function handleTextSizeChange(e) {
    const value = parseInt(e.target.value);
    AppState.designSettings.textSize = value;
    elements.textSizeValue.textContent = value > 0 ? '+' + value : value;
    renderCurrentSlide();
}

/**
 * Reset design settings
 */
function resetDesignSettings() {
    AppState.designSettings = {
        primaryColor: '#000000',
        accentColor: '#FF6B9D',
        backgroundColor: '#FFFFFF',
        headingFont: 'Poppins',
        bodyFont: 'Inter',
        padding: 80,
        textSize: 0,
        alignment: 'left'
    };

    // Update UI
    elements.primaryColor.value = '#000000';
    elements.accentColor.value = '#FF6B9D';
    elements.backgroundColor.value = '#FFFFFF';
    elements.headingFont.value = 'Poppins';
    elements.bodyFont.value = 'Inter';
    elements.paddingSlider.value = 80;
    elements.paddingValue.textContent = '80';
    elements.textSizeSlider.value = 0;
    elements.textSizeValue.textContent = '0';
    elements.textAlignment.value = 'left';

    renderCurrentSlide();
}

/**
 * Render current slide on canvas
 */
function renderCurrentSlide() {
    if (typeof CanvasGenerator !== 'undefined' && AppState.carouselData) {
        const slideKeys = Object.keys(AppState.carouselData);
        const currentSlideKey = slideKeys[AppState.currentSlideIndex];
        const slideData = AppState.carouselData[currentSlideKey];

        CanvasGenerator.renderSlide(slideData, AppState.currentSlideIndex + 1);
    }
}

/**
 * Show previous slide
 */
function showPreviousSlide() {
    if (AppState.currentSlideIndex > 0) {
        AppState.currentSlideIndex--;
        elements.currentSlideNum.textContent = AppState.currentSlideIndex + 1;
        renderCurrentSlide();
    }
}

/**
 * Show next slide
 */
function showNextSlide() {
    const totalSlides = Object.keys(AppState.carouselData).length;
    if (AppState.currentSlideIndex < totalSlides - 1) {
        AppState.currentSlideIndex++;
        elements.currentSlideNum.textContent = AppState.currentSlideIndex + 1;
        renderCurrentSlide();
    }
}

/**
 * Handle generate images
 */
async function handleGenerateImages() {
    if (!AppState.carouselData) {
        showAlert('No carousel data available', 'warning');
        return;
    }

    navigateToStep(4);

    try {
        // Show loading
        elements.loadingSpinner.classList.remove('d-none');
        elements.downloadSection.classList.add('d-none');

        const images = [];

        // Generate all 10 slides
        const slideKeys = Object.keys(AppState.carouselData);
        for (let i = 0; i < slideKeys.length; i++) {
            const slideKey = slideKeys[i];
            const slideData = AppState.carouselData[slideKey];

            // Update progress
            const progress = ((i + 1) / slideKeys.length) * 100;
            elements.progressBar.style.width = progress + '%';

            // Render slide
            if (typeof CanvasGenerator !== 'undefined') {
                const imageData = await CanvasGenerator.generateSlideImage(slideData, i + 1);
                images.push({
                    name: `slide-${i + 1}.png`,
                    data: imageData
                });
            }

            // Small delay for UX
            await sleep(200);
        }

        // Create and download ZIP
        await createAndDownloadZip(images);

        // Show download section
        elements.loadingSpinner.classList.add('d-none');
        elements.downloadSection.classList.remove('d-none');

    } catch (error) {
        console.error('Error generating images:', error);
        showAlert('Failed to generate images: ' + error.message, 'danger');
    }
}

/**
 * Create and download ZIP file
 */
async function createAndDownloadZip(images) {
    // Note: This requires JSZip library or server-side ZIP creation
    // For now, we'll provide individual downloads
    // In production, implement proper ZIP creation

    console.log('Generated images:', images);

    // Store images for download
    AppState.generatedImages = images;

    // Update download button
    elements.downloadZipBtn.addEventListener('click', (e) => {
        e.preventDefault();
        downloadAllImages();
    });
}

/**
 * Download all images
 */
function downloadAllImages() {
    if (!AppState.generatedImages) return;

    AppState.generatedImages.forEach((image, index) => {
        setTimeout(() => {
            const link = document.createElement('a');
            link.download = image.name;
            link.href = image.data;
            link.click();
        }, index * 500); // Stagger downloads
    });

    showAlert('Download started! Check your downloads folder.', 'success');
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

/**
 * Sleep utility
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
