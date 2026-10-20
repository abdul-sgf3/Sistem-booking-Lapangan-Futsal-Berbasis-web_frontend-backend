<?php
// admin/includes/footer_admin.php - Footer Admin Tsubasa Arena
?>
    </div> <!-- .main-content -->
    
    <!-- FOOTER -->
    <footer style="background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%); color: white; padding: 25px 0 15px; margin-top: 30px;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 30px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-bottom: 20px;">
                <div>
                    <h4 style="color: #e63946; margin-bottom: 12px; font-size: 14px;">⚡ Vincent Arena</h4>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); line-height: 1.5;">Booking lapangan futsal dengan mudah dan cepat. Terbang tinggi seperti Tsubasa!</p>
                </div>
                <div>
                    <h4 style="color: #e63946; margin-bottom: 12px; font-size: 14px;">📌 Link Cepat</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 6px;"><a href="index.php" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 12px;">Dashboard</a></li>
                        <li style="margin-bottom: 6px;"><a href="lapangan.php" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 12px;">Kelola Lapangan</a></li>
                        <li style="margin-bottom: 6px;"><a href="booking.php" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 12px;">Kelola Booking</a></li>
                        <li style="margin-bottom: 6px;"><a href="user.php" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 12px;">Kelola User</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: #e63946; margin-bottom: 12px; font-size: 14px;">📞 Kontak</h4>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.7);">📧 info@tsubasaarena.com</p>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.7);">📍 Jakarta, Indonesia</p>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.7);">📅 <?= date('d F Y') ?></p>
                </div>
            </div>
            <div style="text-align: center; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 11px; color: rgba(255,255,255,0.5);">
                <p>&copy; <?= date('Y') ?> Tsubasa Arena – Admin Panel ⚡⚽</p>
            </div>
        </div>
    </footer>
</body>
</html>