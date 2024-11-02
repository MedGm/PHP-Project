<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliments for Ikramoos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="love.png">
</head>
<body> 
<div class="container"> 
    <header>
        <h1>Special Compliments for You 💖</h1>
        <pre><center>Hey Ikram, someone special made this just for you. Ready to hear some sweet words?</center></pre>
    </header>
    
    <div class="gif_container1">
        <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExcDdtZ2JiZDR0a3lvMWF4OG8yc3p6Ymdvd3g2d245amdveDhyYmx6eCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/cLS1cfxvGOPVpf9g3y/giphy.gif" class="complimenter" style="width: 300px; height: 300px;">
    </div>

    <div id="compliment" class="compliment"></div>

    <button id="hearComplimentsButton" onclick="showCompliments()">Hear Compliments</button>
    
    <!-- Floating Hearts -->
    <div class="hearts">
        <div class="heart"></div>
        <div class="heart"></div>
        <div class="heart"></div>
    </div>
    <div class="balloon" style="left: 10%;">
    <img src="ballon.png" alt="balloon">
</div>
<div class="balloon" style="left: 30%;">
    <img src="ballon2.png" alt="balloon">
</div>
<div class="balloon" style="left: 50%;">
    <img src="ballon.png" alt="balloon">
</div>
<div class="balloon" style="left: 70%;">
    <img src="ballon2.png" alt="balloon">
</div>
<div class="balloon" style="left: 90%;">
    <img src="ballon.png" alt="balloon">
</div>


<div class="confetti" style="left: 20%;"></div>
<div class="confetti" style="left: 40%;"></div>
<div class="confetti" style="left: 60%;"></div>
<div class="confetti" style="left: 80%;"></div>

</div>

<footer>
    <p>Made with 💖 by Med, just for you, Ikramoos!</p>
</footer>

<audio id="birthdayMusic1" src="bdaysong1.mp3" preload="auto" style="display: none;"></audio>
<audio id="birthdayMusic2" src="bdaysong2.mp3" preload="auto" style="display: none;"></audio>
<script>
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

    window.onload = function() {
        playMusic();
    };

    const compliments = [
        "You're absolutely amazing Girlll! 💖",
        "You Know that you light up every room you enter. 💖",
        "Annnd You have the kindest heart. ❤️",
        "Every moment with you is a blessing for me. 💖",
        "You make my days better and I wanna make yours better too! ❤️",
        "You're the best thing that happened to me, Ikramoos! 💖",
        "I will do anything for you Sweeetieee! ❤️",
        "You deserve the queen title and the queen treatment, and I will forever give it to you! 💖",
        "You are my queen, and I love you so much, Ikramooos❤️",
        "You know I made this for you because I love you so much and I want to know if you do too? <br> (don't worry I won't know the answer 😂) ❤️"
    ];
    
    let index = 0;

    function showCompliments() {
        if (index < compliments.length - 1) {
            document.getElementById('compliment').innerText = compliments[index];
            index++;
        } else {
            document.getElementById('compliment').innerHTML = `
                ${compliments[index]} 
                <button onclick="askOut('yes')">Yes</button> 
                <button onclick="askOut('no')">No</button>
            `;
            document.getElementById('hearComplimentsButton').style.display = 'none';
        }
        createHearts();
    }

    function askOut(answer) {
        if (answer === 'yes') {
            document.getElementById('compliment').innerText = "Yaaay! He will be so happy if he knows your answer! He loves you so much! 💖";
        } else {
            document.getElementById('compliment').innerText = "Are you sure about that? 😢";
            setTimeout(() => {
                showCompliments();
            }, 3000); // Repeats the question after 3 seconds
        }
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
</script>
</body>
</html>