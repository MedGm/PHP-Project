function showmsg() {
    document.getElementById('message').innerHTML = `<pre>
Ikramoos, today is all about you, and I just want to remind you how much 
I adore you! 💖 You make me the happiest person in the world, 
and I’m so grateful since the first time I met you. ❤️. Your smile, your love, 
your presence, everything about you makes life better. ❤️

We are so lucky to have you in this world. You’re everything we all needed, 
You are more than what words can express, You will always mean so much to me❤️

Happy birthday Queen! 👑❤️
Always yours ♥️
</pre>`;
    document.getElementById('showmsg').style.display = 'none';
    document.getElementById('hearCompliments').style.display = 'block';
    createHearts();
    playMusic();
    playConfettiVideo();
}

function playConfettiVideo() {
    const video = document.getElementById('confettiVideo');
    video.style.display = 'block';
    video.play();
}

// Countdown Timer
function countdownTimer() {
    const countdownElement = document.getElementById('countdown');
    const countdownOverlay = document.getElementById('countdownOverlay');
    const targetDate = new Date(2024, 10, 27).getTime(); // 27 November 2024

    function updateTimer() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

        if (distance < 0) {
            clearInterval(timerInterval);
            countdownElement.innerHTML = "Happy Birthday!";
            countdownOverlay.style.display = 'none'; // Hide the overlay
            localStorage.setItem('countdownEnded', 'true'); // Set flag in localStorage
        }
    }

    updateTimer();
    var timerInterval = setInterval(updateTimer, 1000);
}

window.onload = function() {
    const targetDate = new Date(2024, 10, 27).getTime(); // 27 November 2024
    const now = new Date().getTime();
    const distance = targetDate - now;

    // Check if the countdown has ended or if the current date is past the target date
    if (localStorage.getItem('countdownEnded') === 'true' && distance < 0) {
        document.getElementById('countdownOverlay').style.display = 'none';
    } else {
        // Reset the countdownEnded flag if the target date is changed
        localStorage.removeItem('countdownEnded');
        document.getElementById('countdownOverlay').style.display = 'flex';
        countdownTimer();
    }
};

const music1 = document.getElementById('birthdayMusic1');
const music2 = document.getElementById('birthdayMusic2');

function playMusic() {
    music1.play();
    music1.addEventListener('ended', () => {
        music2.play();
    });

    music2.addEventListener('ended', () => {
        music1.play();
    });
}

function hearCompliments() {
    createHearts();
    setTimeout(() => {
        location.href = 'compliment.php';
    }, 1500); // Redirect after 1.5 second
}

function createHearts() {
    const heartContainer = document.createElement('div');
    heartContainer.classList.add('heart-container');
    document.body.appendChild(heartContainer);

    for (let i = 0; i < 10; i++) {
        const heart = document.createElement('div');
        heart.classList.add('animated-heart');
        heart.style.left = `${Math.random() * 100}%`;
        heart.style.animationDuration = `${Math.random() * 2 + 3}s`;
        heartContainer.appendChild(heart);
    }

    setTimeout(() => {
        heartContainer.remove();
    }, 5000); // Remove hearts after 5 seconds
}