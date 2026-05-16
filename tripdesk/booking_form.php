<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$flight_id = $_GET['id'] ?? null;
if (!$flight_id) { die("Let nebyl vybrán."); }

// Načteme info o letu
$stmt = $pdo->prepare("SELECT f.*, a.name as airline_name, a.logo FROM flights f JOIN airlines a ON f.airline_id = a.id WHERE f.id = ?");
$stmt->execute([$flight_id]);
$flight = $stmt->fetch();

if (!$flight) { die("Let neexistuje."); }

include 'includes/header.php'; 
?>

<div class="container" style="max-width: 900px; margin-top: 40px;">
    <header style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: var(--primary);">🛡️ Dokončení rezervace</h1>
        <p style="color: #718096;">Zkontrolujte údaje a potvrďte svou cestu.</p>
    </header>
    
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; align-items: start;">
        
        <div class="card" style="background: #f8fafc; border-top: 4px solid var(--secondary);">
            <h3 style="margin-top: 0; color: var(--primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">✈️ Shrnutí letu</h3>
            
            <div style="margin: 20px 0;">
                <small style="color: #a0aec0; font-weight: bold; text-transform: uppercase;">Trasa</small>
                <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">
                    <?= htmlspecialchars($flight['destination_from']); ?> ➔ <?= htmlspecialchars($flight['destination_to']); ?>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <small style="color: #a0aec0; font-weight: bold; text-transform: uppercase;">Společnost</small>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                    <strong><?= htmlspecialchars($flight['airline_name']); ?></strong>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <small style="color: #a0aec0; font-weight: bold; text-transform: uppercase;">Čas odletu</small>
                <div>📅 <?= date('d. m. Y', strtotime($flight['departure_time'])); ?></div>
                <div style="font-size: 1.1rem; font-weight: bold; color: var(--secondary);">🕒 <?= date('H:i', strtotime($flight['departure_time'])); ?></div>
            </div>

            <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #edf2f7; text-align: center; margin-top: 30px;">
                <span style="color: #718096;">Celková cena k úhradě:</span>
                <div style="font-size: 1.6rem; color: var(--success); font-weight: 800;">
                    <?= number_format($flight['price'], 0, ',', ' '); ?> Kč
                </div>
            </div>
        </div>

        <div class="card" style="border-top: 4px solid var(--success);">
            <h3 style="margin-top: 0; color: var(--primary);">👤 Údaje o cestujícím</h3>
            
            <form action="actions/save_booking.php" method="POST">
                <input type="hidden" name="flight_id" value="<?= $flight['id']; ?>">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Jméno a příjmení cestujícího:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="passenger_first_name" 
                               placeholder="Jméno"
                               value="<?= htmlspecialchars($_SESSION['first_name'] ?? ''); ?>" 
                               required>
                        <input type="text" name="passenger_last_name" 
                               placeholder="Příjmení"
                               value="<?= htmlspecialchars($_SESSION['last_name'] ?? ''); ?>" 
                               required>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Kontaktní e-mail:</label>
                    <input type="email" name="contact_email" 
                           value="<?= htmlspecialchars($_SESSION['email'] ?? ''); ?>" 
                           placeholder="vas@email.cz"
                           required>
                    <small style="color: #a0aec0;">Na tento e-mail vám zašleme letenku.</small>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Telefonní číslo:</label>
                    <input type="text" name="contact_phone" placeholder="+420 000 000 000" required>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; padding: 18px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <span>💳</span> Potvrdit a rezervovat let
                </button>
                
                <p style="text-align: center; font-size: 0.8rem; color: #a0aec0; margin-top: 15px;">
                    Kliknutím na tlačítko souhlasíte s obchodními podmínkami TripDesk.
                </p>
            </form>
        </div>
    </div>
</div>