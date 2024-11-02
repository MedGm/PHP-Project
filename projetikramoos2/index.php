<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday Ikramoos</title>
    <link rel="icon" href="love.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="countdownOverlay" style="display: none;">
    <div id="countdown"></div>
    <div id="countdownMessage">until the best day of the year <3</div>
</div>
<div class="container">
    <header>
        <h1>IKRAMOOS Happy Birthdayy🎉❤️</h1>
        <button id="showmsg" onclick="showmsg()" name="showmsg">click heree!</button>
        <div id="message"></div>
        <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExb20zbmxrN3JtcnZzb3RqdG11M3l5NjR3bHl4bmY5NjdmYTcwZTc4bCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/5bdhq6YF0szPaCEk9Y/giphy.webp" class="kisses">
    </header>
    <div class="gif_container">
        <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExZm1vbnFvdXZxN2FhcndzMXVzN3A4bXR1MmpiaW1iY3k3czB5a2ZlZSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/fVyNy7A3DK2WQS68fM/giphy.webp" class="bunny">
        <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExZnY5MmUyMm5lYWFvZHVkOGF0eHhtNDc3bDZ5OXU4cGVjdnE4dTdwaSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/KztT2c4u8mYYUiMKdJ/giphy.webp" class="cats">
        <img src="bday.jpg" class="bday">
    </div>
    <button id="hearCompliments" onclick="hearCompliments()" name="hearCompliments" style="display: none;">wanna hear some compliments ?</button>
    <div class="hearts">
        <div class="heart"></div>
        <div class="heart"></div>
        <div class="heart"></div>
    </div>
</div>
<footer>
    <p>Made with 💖 by Med, just for you, Ikramoos!</p>
</footer>

<audio id="birthdayMusic1" src="bdaysong1.mp3" preload="auto" style="display: none;"></audio>
<audio id="birthdayMusic2" src="bdaysong2.mp3" preload="auto" style="display: none;"></audio>

<!-- Add Balloons and Confetti -->
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

<!-- Add the video element -->
<video id="confettiVideo" class="background-video" src="confetti.mp4" preload="auto" loop muted></video>

<script src="script.js"></script>
</body>
</html>