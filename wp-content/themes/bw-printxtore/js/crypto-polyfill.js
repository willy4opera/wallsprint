(function() {
    // Polyfill for crypto
    if (!window.crypto) {
        window.crypto = {};
    }
    
    // Polyfill for crypto.getRandomValues if needed
    if (!window.crypto.getRandomValues) {
        window.crypto.getRandomValues = function(array) {
            for (let i = 0; i < array.length; i++) {
                array[i] = Math.floor(Math.random() * 256);
            }
            return array;
        };
    }

    // Polyfill for crypto.randomUUID
    if (!window.crypto.randomUUID) {
        window.crypto.randomUUID = function() {
            let d = new Date().getTime();
            let d2 = (typeof performance !== 'undefined' && performance.now && performance.now() * 1000) || 0;
            
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                let r = Math.random() * 16;
                if (d > 0) {
                    r = (d + r) % 16 | 0;
                    d = Math.floor(d / 16);
                } else {
                    r = (d2 + r) % 16 | 0;
                    d2 = Math.floor(d2 / 16);
                }
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        };
    }
})();

// Add console warning for debugging if using polyfill
if (!window.crypto.randomUUID) {
    console.warn('Using crypto.randomUUID polyfill. This is a fallback implementation and may not provide the same level of randomness as the native implementation.');
}
