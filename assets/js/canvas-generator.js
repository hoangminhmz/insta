/**
 * Instagram Carousel Generator - Canvas Generator
 * Uses Fabric.js to create carousel slide images
 */

const CanvasGenerator = {
    canvas: null,
    appState: null,
    canvasWidth: 1080,
    canvasHeight: 1350,
    totalSlides: 10, // Will be updated dynamically

    /**
     * Initialize canvas
     */
    init(canvasElement, appState) {
        this.appState = appState;

        // Create Fabric canvas
        this.canvas = new fabric.Canvas(canvasElement, {
            width: this.canvasWidth,
            height: this.canvasHeight,
            backgroundColor: appState.designSettings.backgroundColor
        });

        // Scale canvas for display
        this.scaleCanvasForDisplay();

        return this.canvas;
    },

    /**
     * Scale canvas for responsive display
     */
    scaleCanvasForDisplay() {
        const container = document.getElementById('canvasContainer');
        const containerWidth = container.offsetWidth || 600;
        const scale = Math.min(containerWidth / this.canvasWidth, 0.5);

        this.canvas.setZoom(scale);
        this.canvas.setWidth(this.canvasWidth * scale);
        this.canvas.setHeight(this.canvasHeight * scale);
    },

    /**
     * Render slide based on type
     */
    renderSlide(slideData, slideNumber) {
        this.canvas.clear();
        this.canvas.backgroundColor = this.appState.designSettings.backgroundColor;

        switch (slideData.type) {
            case 'hook':
                this.renderHookSlide(slideData, slideNumber);
                break;
            case 'content':
                this.renderContentSlide(slideData, slideNumber);
                break;
            case 'list':
                this.renderListSlide(slideData, slideNumber);
                break;
            case 'bonus':
                this.renderBonusSlide(slideData, slideNumber);
                break;
            case 'cta':
                this.renderCTASlide(slideData, slideNumber);
                break;
            default:
                this.renderDefaultSlide(slideData, slideNumber);
        }

        this.canvas.renderAll();
    },

    /**
     * Render Hook Slide (Slide 1)
     */
    renderHookSlide(slideData, slideNumber) {
        const { padding } = this.appState.designSettings;
        const centerY = this.canvasHeight / 2;

        // Add emoji if present
        if (slideData.emoji) {
            const emoji = new fabric.Text(slideData.emoji, {
                left: this.canvasWidth / 2,
                top: centerY - 200,
                fontSize: 120,
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });
            this.canvas.add(emoji);
        }

        // Main hook text
        const hookText = this.createText(slideData.text, {
            left: this.canvasWidth / 2,
            top: centerY,
            fontSize: 68 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            textAlign: 'center',
            originX: 'center',
            originY: 'center',
            fill: this.appState.designSettings.primaryColor,
            width: this.canvasWidth - (padding * 2)
        });
        this.canvas.add(hookText);

        // Highlight keywords
        this.highlightKeywords(hookText, slideData.keywords);

        // Slide number indicator
        this.addSlideNumber(slideNumber);
    },

    /**
     * Render Content Slide (Slides 2-6)
     */
    renderContentSlide(slideData, slideNumber) {
        const { padding, primaryColor, accentColor } = this.appState.designSettings;
        let yPosition = padding + 100;

        // Heading
        const heading = this.createText(slideData.heading, {
            left: padding,
            top: yPosition,
            fontSize: 58 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            fill: primaryColor,
            width: this.canvasWidth - (padding * 2)
        });
        this.canvas.add(heading);
        yPosition += heading.height + 60;

        // Accent line
        const line = new fabric.Rect({
            left: padding,
            top: yPosition,
            width: 80,
            height: 6,
            fill: accentColor
        });
        this.canvas.add(line);
        yPosition += 50;

        // Body text
        const body = this.createText(slideData.body, {
            left: padding,
            top: yPosition,
            fontSize: 38 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.bodyFont,
            fill: '#333333',
            width: this.canvasWidth - (padding * 2),
            lineHeight: 1.6
        });
        this.canvas.add(body);

        // Highlight keywords in body
        this.highlightKeywords(body, slideData.keywords);

        // Slide number
        this.addSlideNumber(slideNumber);
    },

    /**
     * Render List Slide (Slides 7-8)
     */
    renderListSlide(slideData, slideNumber) {
        const { padding, primaryColor, accentColor } = this.appState.designSettings;
        let yPosition = padding + 100;

        // Heading
        const heading = this.createText(slideData.heading, {
            left: padding,
            top: yPosition,
            fontSize: 58 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            fill: primaryColor,
            width: this.canvasWidth - (padding * 2)
        });
        this.canvas.add(heading);
        yPosition += heading.height + 80;

        // List items
        if (slideData.items && Array.isArray(slideData.items)) {
            slideData.items.forEach((item, index) => {
                // Bullet/number background circle
                const bullet = new fabric.Circle({
                    left: padding,
                    top: yPosition,
                    radius: 25,
                    fill: accentColor
                });
                this.canvas.add(bullet);

                // Bullet number
                const bulletNumber = new fabric.Text((index + 1).toString(), {
                    left: padding,
                    top: yPosition,
                    fontSize: 28,
                    fontFamily: this.appState.designSettings.bodyFont,
                    fontWeight: 'bold',
                    fill: '#FFFFFF',
                    originX: 'center',
                    originY: 'center'
                });
                this.canvas.add(bulletNumber);

                // List item text
                const itemText = this.createText(item, {
                    left: padding + 70,
                    top: yPosition - 20,
                    fontSize: 34 + this.appState.designSettings.textSize,
                    fontFamily: this.appState.designSettings.bodyFont,
                    fill: '#333333',
                    width: this.canvasWidth - (padding * 2) - 80,
                    lineHeight: 1.5
                });
                this.canvas.add(itemText);

                yPosition += Math.max(itemText.height, 50) + 60;
            });
        }

        // Slide number
        this.addSlideNumber(slideNumber);
    },

    /**
     * Render Bonus Slide (Slide 9)
     */
    renderBonusSlide(slideData, slideNumber) {
        const { padding, primaryColor, accentColor } = this.appState.designSettings;
        let yPosition = padding + 100;

        // "BONUS" badge
        const bonusBadge = new fabric.Rect({
            left: padding,
            top: yPosition,
            width: 180,
            height: 60,
            fill: accentColor,
            rx: 30,
            ry: 30
        });
        this.canvas.add(bonusBadge);

        const bonusText = new fabric.Text('BONUS', {
            left: padding + 90,
            top: yPosition + 30,
            fontSize: 28,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            fill: '#FFFFFF',
            originX: 'center',
            originY: 'center'
        });
        this.canvas.add(bonusText);
        yPosition += 120;

        // Heading
        const heading = this.createText(slideData.heading, {
            left: padding,
            top: yPosition,
            fontSize: 58 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            fill: primaryColor,
            width: this.canvasWidth - (padding * 2)
        });
        this.canvas.add(heading);
        yPosition += heading.height + 60;

        // Body
        const body = this.createText(slideData.body, {
            left: padding,
            top: yPosition,
            fontSize: 38 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.bodyFont,
            fill: '#333333',
            width: this.canvasWidth - (padding * 2),
            lineHeight: 1.6
        });
        this.canvas.add(body);

        // Highlight keywords
        this.highlightKeywords(body, slideData.keywords);

        // Slide number
        this.addSlideNumber(slideNumber);
    },

    /**
     * Render CTA Slide (Slide 10)
     */
    renderCTASlide(slideData, slideNumber) {
        const { padding, primaryColor, accentColor } = this.appState.designSettings;
        const centerY = this.canvasHeight / 2;

        // CTA text
        const ctaText = this.createText(slideData.text, {
            left: this.canvasWidth / 2,
            top: centerY - 150,
            fontSize: 52 + this.appState.designSettings.textSize,
            fontFamily: this.appState.designSettings.headingFont,
            fontWeight: 'bold',
            textAlign: 'center',
            originX: 'center',
            originY: 'center',
            fill: primaryColor,
            width: this.canvasWidth - (padding * 2)
        });
        this.canvas.add(ctaText);

        // Decorative line
        const line1 = new fabric.Rect({
            left: this.canvasWidth / 2 - 100,
            top: centerY + 50,
            width: 200,
            height: 4,
            fill: accentColor
        });
        this.canvas.add(line1);

        // Username
        const username = new fabric.Text(slideData.username || `@${this.appState.username}`, {
            left: this.canvasWidth / 2,
            top: centerY + 100,
            fontSize: 44,
            fontFamily: this.appState.designSettings.bodyFont,
            fontWeight: '600',
            textAlign: 'center',
            originX: 'center',
            originY: 'center',
            fill: primaryColor
        });
        this.canvas.add(username);

        // Social icons text
        const socialText = new fabric.Text('❤️  💬  🔖  ↗️', {
            left: this.canvasWidth / 2,
            top: centerY + 180,
            fontSize: 40,
            textAlign: 'center',
            originX: 'center',
            originY: 'center'
        });
        this.canvas.add(socialText);

        // Hashtags (if present)
        if (slideData.hashtags) {
            const hashtags = new fabric.Text(slideData.hashtags, {
                left: this.canvasWidth / 2,
                top: this.canvasHeight - padding - 50,
                fontSize: 24,
                fontFamily: this.appState.designSettings.bodyFont,
                textAlign: 'center',
                originX: 'center',
                fill: '#666666'
            });
            this.canvas.add(hashtags);
        }
    },

    /**
     * Create text object with word wrapping
     */
    createText(text, options) {
        const textObj = new fabric.Textbox(text, {
            ...options,
            splitByGrapheme: true
        });
        return textObj;
    },

    /**
     * Highlight keywords in text with background rectangles
     */
    highlightKeywords(textObj, keywords) {
        if (!keywords || !Array.isArray(keywords) || keywords.length === 0) return;

        const text = textObj.text.toLowerCase();
        const textLeft = textObj.left;
        const textTop = textObj.top;
        const fontSize = textObj.fontSize;
        const lineHeight = textObj.lineHeight || 1.16;

        keywords.forEach(keyword => {
            if (!keyword || keyword.trim() === '') return;

            const searchTerm = keyword.toLowerCase();
            let startIndex = 0;

            // Find all occurrences of the keyword
            while ((startIndex = text.indexOf(searchTerm, startIndex)) !== -1) {
                // Calculate approximate position
                const beforeText = text.substring(0, startIndex);
                const lines = beforeText.split('\n');
                const lineNumber = lines.length - 1;
                const charInLine = lines[lines.length - 1].length;

                // Approximate positioning (not pixel-perfect but good enough)
                const charWidth = fontSize * 0.6; // Approximate character width
                const xPos = textLeft + (charInLine * charWidth);
                const yPos = textTop + (lineNumber * fontSize * lineHeight);

                // Create highlight rectangle
                const highlight = new fabric.Rect({
                    left: xPos - 4,
                    top: yPos - 2,
                    width: keyword.length * charWidth + 8,
                    height: fontSize + 4,
                    fill: 'rgba(42, 176, 162, 0.2)', // Teal with transparency
                    rx: 4,
                    ry: 4,
                    selectable: false
                });

                // Add behind the text
                this.canvas.insertAt(highlight, this.canvas.getObjects().indexOf(textObj));

                startIndex += searchTerm.length;
            }
        });
    },

    /**
     * Add slide number indicator
     */
    addSlideNumber(slideNumber) {
        const slideNum = new fabric.Text(`${slideNumber}/${this.totalSlides}`, {
            left: this.canvasWidth - 80,
            top: this.canvasHeight - 80,
            fontSize: 24,
            fontFamily: this.appState.designSettings.bodyFont,
            fill: '#999999',
            originX: 'center',
            originY: 'center'
        });
        this.canvas.add(slideNum);
    },

    /**
     * Set total slides count
     */
    setTotalSlides(count) {
        this.totalSlides = count;
    },

    /**
     * Render default slide (fallback)
     */
    renderDefaultSlide(slideData, slideNumber) {
        const text = new fabric.Text('Slide ' + slideNumber, {
            left: this.canvasWidth / 2,
            top: this.canvasHeight / 2,
            fontSize: 48,
            fontFamily: this.appState.designSettings.headingFont,
            fill: this.appState.designSettings.primaryColor,
            originX: 'center',
            originY: 'center'
        });
        this.canvas.add(text);
        this.addSlideNumber(slideNumber);
    },

    /**
     * Generate slide as image data URL
     */
    async generateSlideImage(slideData, slideNumber) {
        return new Promise((resolve) => {
            // Clear and render slide at full resolution
            this.canvas.setZoom(1);
            this.canvas.setWidth(this.canvasWidth);
            this.canvas.setHeight(this.canvasHeight);

            this.renderSlide(slideData, slideNumber);

            // Get image data
            const imageData = this.canvas.toDataURL({
                format: 'png',
                quality: 1,
                multiplier: 1
            });

            // Restore zoom for preview
            this.scaleCanvasForDisplay();

            resolve(imageData);
        });
    },

    /**
     * Download single slide
     */
    downloadSlide(slideNumber) {
        const dataURL = this.canvas.toDataURL({
            format: 'png',
            quality: 1
        });

        const link = document.createElement('a');
        link.download = `slide-${slideNumber}.png`;
        link.href = dataURL;
        link.click();
    }
};

// Make CanvasGenerator globally available
window.CanvasGenerator = CanvasGenerator;
