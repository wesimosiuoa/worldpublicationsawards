<!-- Loading Indicator -->
<div id="loadingIndicator" class="position-fixed top-0 start-0 w-100" style="z-index: 9999; height: 3px; display: none;">
    <div class="progress" style="height: 3px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 100%; height: 3px;"></div>
    </div>
</div>

<script>
    function showLoading() {
        document.getElementById('loadingIndicator').style.display = 'block';
    }
    
    function hideLoading() {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
    
    // Override the global fetch function to show loader for all requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        showLoading();
        return originalFetch.apply(this, args)
            .finally(() => {
                hideLoading();
            });
    };
    
    // Also override XMLHttpRequest to show loader for all AJAX requests
    const originalXHR = window.XMLHttpRequest;
    window.XMLHttpRequest = function() {
        const xhr = new originalXHR();
        
        const originalOpen = xhr.open;
        xhr.open = function(...args) {
            originalOpen.apply(this, args);
        };
        
        const originalSend = xhr.send;
        xhr.send = function(...args) {
            showLoading();
            const loadendHandler = () => {
                hideLoading();
                xhr.removeEventListener('loadend', loadendHandler);
            };
            xhr.addEventListener('loadend', loadendHandler);
            return originalSend.apply(this, args);
        };
        
        return xhr;
    };
</script>

</body>
</html>