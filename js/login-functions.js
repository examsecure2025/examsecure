// Import Firebase modules
import { signInWithPopup, GoogleAuthProvider } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js';
import { doc, setDoc, getDoc } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js';

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        const errorMessage = document.getElementById('error-message');
        const errorText = errorMessage.querySelector('.error-text');
        errorText.textContent = decodeURIComponent(error);
        errorMessage.style.display = 'flex';
        
        document.querySelectorAll('input').forEach(input => {
            input.classList.add('error-input');
        });
    } else {
        const errorMessage = document.getElementById('error-message');
        errorMessage.style.display = 'none';
    }

    // Initialize Google Sign-In button
    initializeGoogleSignIn();
});

function initializeGoogleSignIn() {
    const googleBtn = document.querySelector('.google-btn');
    if (googleBtn) {
        googleBtn.addEventListener('click', handleGoogleSignIn);
    }
}

async function handleGoogleSignIn() {
    try {
        // Show loading state
        const googleBtn = document.querySelector('.google-btn');
        const originalText = googleBtn.innerHTML;
        googleBtn.innerHTML = '<div class="spinner"></div> Signing in...';
        googleBtn.disabled = true;

        // Sign in with Google
        const result = await signInWithPopup(window.firebaseAuth, window.googleProvider);
        const user = result.user;

        // Prepare user data for Firestore
        const userData = {
            studentId: user.uid,
            displayName: user.displayName,
            email: user.email,
            photoURL: user.photoURL,
            createdAt: new Date().toISOString(),
            lastLoginAt: new Date().toISOString(),
            yearSection: extractYearSection(user.email),
            lastUsedYearSection: extractYearSection(user.email)
        };

        // Save/update user data in Firestore
        await saveUserToFirestore(user.uid, userData);

        // Send user data to PHP backend for session management
        await sendUserDataToBackend(userData);

        // Redirect to dashboard
        window.location.href = 'dashboard.php';

    } catch (error) {
        console.error('Google Sign-In Error:', error);
        showError('Google sign-in failed. Please try again.');
        
        // Reset button state
        const googleBtn = document.querySelector('.google-btn');
        googleBtn.innerHTML = originalText;
        googleBtn.disabled = false;
    }
}

async function saveUserToFirestore(userId, userData) {
    try {
        const userRef = doc(window.firebaseDb, 'students', userId);
        await setDoc(userRef, userData, { merge: true });
        console.log('User data saved to Firestore');
    } catch (error) {
        console.error('Error saving to Firestore:', error);
        throw error;
    }
}

async function sendUserDataToBackend(userData) {
    try {
        const response = await fetch('auth/processGoogleLogin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });

        if (!response.ok) {
            throw new Error('Failed to create session');
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Session creation failed');
        }
    } catch (error) {
        console.error('Error creating session:', error);
        throw error;
    }
}

function extractYearSection(email) {
    // Extract year and section from institutional email
    // Example: 22ln3062_ms@psu.edu.ph -> "IV BSIT-B"
    const match = email.match(/(\d{2})ln(\d+)_(\w+)@psu\.edu\.ph/);
    if (match) {
        const year = match[1];
        const section = match[3];
        // Convert year to Roman numeral (simplified)
        const yearMap = {
            '22': 'IV',
            '23': 'III', 
            '24': 'II',
            '25': 'I'
        };
        return `${yearMap[year] || 'IV'} BSIT-${section}`;
    }
    return 'Unknown';
}

function showError(message) {
    const errorMessage = document.getElementById('error-message');
    const errorText = errorMessage.querySelector('.error-text');
    errorText.textContent = message;
    errorMessage.style.display = 'flex';
}

// Ripple effect for Google button
document.querySelectorAll('.google-btn').forEach(btn => {
    btn.addEventListener('mouseover', function(e) {
      const rect = btn.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      ripple.style.width = ripple.style.height = Math.max(rect.width, rect.height) + 'px';
      ripple.style.left = (e.clientX - rect.left - rect.width/2) + 'px';
      ripple.style.top = (e.clientY - rect.top - rect.height/2) + 'px';
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });
});
  