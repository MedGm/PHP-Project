</div>
    </div> 
    
    <footer>
        <p>Système de management des étudiants UAE - Tous droits réservés &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script>
    function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('active');
    }

    function showProfile() {
        window.location.href = 'profile.php';
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.user-menu')) {
            document.getElementById('userDropdown').classList.remove('active');
        }
    });
    </script>
</body>
</html>