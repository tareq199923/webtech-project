<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room = isset($_POST['room']) ? $_POST['room'] : 'No room selected';
    $upgrade = isset($_POST['upgrade']) ? $_POST['upgrade'] : 'No upgrade selected';

    // Prices for rooms and upgrades
    $room_prices = [
        'Standard Room' => 100,
        'Deluxe Room' => 150,
        'Suite' => 200
    ];

    $upgrade_prices = [
        'Breakfast Included' => 20,
        'Airport Pickup' => 30,
        'All Inclusive' => 50
    ];

    // Calculate total price
    $total_price = 0;

    if (array_key_exists($room, $room_prices)) {
        $total_price += $room_prices[$room];
    }

    if (array_key_exists($upgrade, $upgrade_prices)) {
        $total_price += $upgrade_prices[$upgrade];
    }

    echo "<h1>Booking Confirmation</h1>";
    echo "<p>Room: $room</p>";
    echo "<p>Upgrade: $upgrade</p>";
    echo "<p>Total Price: $$total_price</p>";
} else {
    echo "Invalid request method.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: blue;
        }
        .room-selection, .payment-upgrade {
            margin-bottom: 20px;
        }
        .room {
            background-color: lightgray;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }
        .room label {
            font-weight: bold;
        }
        .upgrade-option {
            background-color: lightyellow;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h1>Room Booking Page</h1>
    
    <div class="room-selection">
        <h2>Select Your Room</h2>
        <form action="process_booking.php" method="POST">
            <div class="room">
                <label for="room1">
                    <input type="radio" name="room" id="room1" value="Standard Room">
                    Standard Room - $100 per night
                </label>
            </div>
            <div class="room">
                <label for="room2">
                    <input type="radio" name="room" id="room2" value="Deluxe Room">
                    Deluxe Room - $150 per night
                </label>
            </div>
            <div class="room">
                <label for="room3">
                    <input type="radio" name="room" id="room3" value="Suite">
                    Suite - $200 per night
                </label>
            </div>

            <div class="payment-upgrade">
                <h2>Payment Upgrade Options</h2>
                <div class="upgrade-option">
                    <label for="upgrade1">
                        <input type="radio" name="upgrade" id="upgrade1" value="Breakfast Included">
                        Breakfast Included - $20
                    </label>
                </div>
                <div class="upgrade-option">
                    <label for="upgrade2">
                        <input type="radio" name="upgrade" id="upgrade2" value="Airport Pickup">
                        Airport Pickup - $30
                    </label>
                </div>
                <div class="upgrade-option">
                    <label for="upgrade3">
                        <input type="radio" name="upgrade" id="upgrade3" value="All Inclusive">
                        All Inclusive - $50
                    </label>
                </div>
            </div>

            <input type="submit" value="Book Now">
        </form>
    </div>
</body>
</html>
