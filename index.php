<?php
session_start();
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Carousel Generator | AI-Powered Content Creator</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <!-- Fabric.js for Canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
</head>
<body>

    <!-- Header -->
    <header class="bg-gradient text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Instagram Carousel Generator</h1>
                    <p class="mb-0 mt-1 opacity-75">Transform your blog content into engaging 10-20 slide carousels with AI</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark">AI-Powered</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container my-5">

        <!-- Step Indicator -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Input Content</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Review Structure</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Customize Design</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Generate & Download</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 1: Input Form -->
        <div id="step1" class="step-content active">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Blog Content Input</h5>
                        </div>
                        <div class="card-body">
                            <form id="contentForm">
                                <div class="mb-3">
                                    <label for="blogURL" class="form-label">
                                        <i class="bi bi-link-45deg me-1"></i>Or Provide Blog URL
                                    </label>
                                    <input
                                        type="url"
                                        class="form-control"
                                        id="blogURL"
                                        placeholder="https://example.com/blog-post"
                                    >
                                    <div class="form-text">
                                        Automatically extract content from any blog URL
                                    </div>
                                </div>

                                <div class="text-center mb-3">
                                    <span class="badge bg-secondary">OR</span>
                                </div>

                                <div class="mb-3">
                                    <label for="blogContent" class="form-label">Paste Your Blog Content</label>
                                    <textarea
                                        class="form-control"
                                        id="blogContent"
                                        rows="10"
                                        placeholder="Paste your blog article here (1000-3000 words recommended)..."
                                        maxlength="10000"
                                    ></textarea>
                                    <div class="form-text">
                                        <span id="charCount">0</span> / 10,000 characters
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nicheSelector" class="form-label">Content Niche</label>
                                        <select class="form-select" id="nicheSelector" required>
                                            <option value="">Select Niche...</option>
                                            <option value="Health & Wellness">Health & Wellness</option>
                                            <option value="Business & Entrepreneurship">Business & Entrepreneurship</option>
                                            <option value="Technology & AI">Technology & AI</option>
                                            <option value="Lifestyle & Personal Development">Lifestyle & Personal Development</option>
                                            <option value="Finance & Investing">Finance & Investing</option>
                                            <option value="Marketing & Social Media">Marketing & Social Media</option>
                                            <option value="Fitness & Nutrition">Fitness & Nutrition</option>
                                            <option value="Education & Learning">Education & Learning</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="toneSelector" class="form-label">Content Tone</label>
                                        <select class="form-select" id="toneSelector" required>
                                            <option value="">Select Tone...</option>
                                            <option value="Educational">Educational</option>
                                            <option value="Motivational">Motivational</option>
                                            <option value="Professional">Professional</option>
                                            <option value="Casual & Friendly">Casual & Friendly</option>
                                            <option value="Inspirational">Inspirational</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Instagram Username (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="username" placeholder="yourusername">
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn">
                                        <i class="bi bi-magic me-2"></i>Analyze Content with AI
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips for Best Results</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Use 1000-3000 word articles</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Clear structure with headings</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Actionable insights included</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Problem-solution format works best</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Include statistics or facts</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3 border-warning">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>What Happens Next?</h6>
                        </div>
                        <div class="card-body">
                            <ol class="small mb-0">
                                <li>AI analyzes your content structure</li>
                                <li>Extracts key points and insights</li>
                                <li>Creates 10-slide carousel outline</li>
                                <li>You review and edit each slide</li>
                                <li>Customize colors and fonts</li>
                                <li>Download high-quality images</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: Review AI Structure -->
        <div id="step2" class="step-content">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Review Your Carousel Slides</h5>
                            <button class="btn btn-light btn-sm" id="regenerateAllBtn">
                                <i class="bi bi-arrow-clockwise me-1"></i>Regenerate All
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="slidesPreview" class="slides-grid">
                                <!-- Slides will be dynamically inserted here -->
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-outline-secondary" id="backToStep1">
                                    <i class="bi bi-arrow-left me-2"></i>Back
                                </button>
                                <button class="btn btn-primary" id="proceedToDesign">
                                    Continue to Design <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: Customize Design -->
        <div id="step3" class="step-content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-palette me-2"></i>Live Preview</h5>
                        </div>
                        <div class="card-body text-center bg-light">
                            <div id="canvasContainer" style="display: inline-block; max-width: 100%;">
                                <canvas id="previewCanvas"></canvas>
                            </div>
                            <div class="mt-3">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" id="prevSlide">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary disabled" id="currentSlideBtn">
                                        Slide <span id="currentSlideNum">1</span> / 10
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" id="nextSlide">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-sliders me-2"></i>Customization Panel</h6>
                        </div>
                        <div class="card-body">

                            <!-- Colors -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Colors</h6>
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <input type="color" class="form-control form-control-color" id="primaryColor" value="#000000">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Accent Color</label>
                                    <input type="color" class="form-control form-control-color" id="accentColor" value="#FF6B9D">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Background</label>
                                    <select class="form-select form-select-sm" id="backgroundColor">
                                        <option value="#FFFFFF">White</option>
                                        <option value="#F8F9FA">Light Gray</option>
                                        <option value="#FFF8F0">Cream</option>
                                        <option value="#F0F8FF">Light Blue</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fonts -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Typography</h6>
                                <div class="mb-3">
                                    <label class="form-label">Heading Font</label>
                                    <select class="form-select form-select-sm" id="headingFont">
                                        <option value="Poppins">Poppins (Bold, Modern)</option>
                                        <option value="Montserrat">Montserrat (Clean, Professional)</option>
                                        <option value="Playfair Display">Playfair Display (Elegant)</option>
                                        <option value="Roboto">Roboto (Minimal, Tech)</option>
                                        <option value="Inter">Inter (Readable, Neutral)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Body Font</label>
                                    <select class="form-select form-select-sm" id="bodyFont">
                                        <option value="Inter">Inter (Clean, Readable)</option>
                                        <option value="Roboto">Roboto (Minimal)</option>
                                        <option value="Poppins">Poppins (Friendly)</option>
                                        <option value="Montserrat">Montserrat (Professional)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Layout -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Layout</h6>
                                <div class="mb-3">
                                    <label class="form-label">Padding: <span id="paddingValue">80</span>px</label>
                                    <input type="range" class="form-range" id="paddingSlider" min="60" max="100" value="80">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Text Size: <span id="textSizeValue">0</span></label>
                                    <input type="range" class="form-range" id="textSizeSlider" min="-10" max="10" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Text Alignment</label>
                                    <select class="form-select form-select-sm" id="textAlignment">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="applyToAll" checked>
                                <label class="form-check-label" for="applyToAll">
                                    Apply to all slides
                                </label>
                            </div>

                            <button class="btn btn-sm btn-outline-primary w-100" id="resetDesign">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary" id="backToStep2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Review
                        </button>
                        <button class="btn btn-success btn-lg" id="generateImages">
                            <i class="bi bi-download me-2"></i>Generate & Download
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: Generate & Download -->
        <div id="step4" class="step-content">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Your Carousel is Ready!</h5>
                        </div>
                        <div class="card-body text-center">
                            <div id="loadingSpinner" class="my-5">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Generating high-quality images...</p>
                                <div class="progress mx-auto" style="max-width: 400px;">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div id="downloadSection" class="d-none">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h4 class="mt-3" id="successMessage">Slides Generated Successfully!</h4>
                                <p class="text-muted">Your Instagram carousel images are ready to download</p>

                                <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                                    <a href="#" id="downloadZipBtn" class="btn btn-success btn-lg">
                                        <i class="bi bi-file-earmark-zip me-2"></i>Download ZIP File
                                    </a>
                                    <button class="btn btn-outline-primary" id="generateAgain">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Generate Another Carousel
                                    </button>
                                </div>

                                <div class="alert alert-info mt-4" role="alert">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Next Steps:</strong> Extract the ZIP file and upload all images to Instagram as a carousel post. Remember to write an engaging caption!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">Instagram Carousel Generator &copy; 2025 | Powered by Google Gemini 2.5 Flash</p>
            <small class="text-secondary">Transform your content into engaging visual stories</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JSZip for ZIP file creation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="assets/js/app.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/canvas-generator.js?v=<?php echo time(); ?>"></script>

</body>
</html>
