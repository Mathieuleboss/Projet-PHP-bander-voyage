<?php
// Page de paiement
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur a des articles dans son panier
if (!isset($_SESSION['cart_id'])) {
    $_SESSION['error_message'] = "Votre panier est vide.";
    header('Location: cart.php');
    exit();
}

$db = getDbConnection();
$cartId = $_SESSION['cart_id'];

// Vérifier si le panier contient des articles
$stmt = $db->prepare("SELECT COUNT(*) as count FROM cart_items WHERE cart_id = :cart_id");
$stmt->bindParam(':cart_id', $cartId);
$stmt->execute();
$result = $stmt->fetch();

if ($result['count'] == 0) {
    $_SESSION['error_message'] = "Votre panier est vide.";
    header('Location: cart.php');
    exit();
}

// Récupérer les articles du panier
function getCartItems($cartId) {
    $db = getDbConnection();
    $stmt = $db->prepare("
        SELECT ci.*, s.sneaker_name, s.price, s.discount_price, si.image_url, sz.size_value, sz.size_type, b.brand_name
        FROM cart_items ci
        JOIN sneakers s ON ci.sneaker_id = s.sneaker_id
        JOIN sizes sz ON ci.size_id = sz.size_id
        LEFT JOIN brands b ON s.brand_id = b.brand_id
        LEFT JOIN (
            SELECT sneaker_id, image_url
            FROM sneaker_images
            WHERE is_primary = 1
            LIMIT 1
        ) si ON s.sneaker_id = si.sneaker_id
        WHERE ci.cart_id = :cart_id
    ");
    $stmt->bindParam(':cart_id', $cartId);
    $stmt->execute();

    return $stmt->fetchAll();
}

$cartItems = getCartItems($cartId);

// Calculer le total du panier
$subtotal = 0;
$total = 0;
$shipping = 0;

foreach ($cartItems as $item) {
    $price = $item['discount_price'] ? $item['discount_price'] : $item['price'];
    $subtotal += $price * $item['quantity'];
}

// Frais de livraison
if ($subtotal > 0 && $subtotal < 100) {
    $shipping = 5.99;
}

$total = $subtotal + $shipping;

// Traitement du formulaire de paiement
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que tous les champs obligatoires sont remplis
    if (
        empty($_POST['first_name']) ||
        empty($_POST['last_name']) ||
        empty($_POST['email']) ||
        empty($_POST['address']) ||
        empty($_POST['city']) ||
        empty($_POST['postal_code']) ||
        empty($_POST['country']) ||
        empty($_POST['payment_method'])
    ) {
        $error_message = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            // Enregistrer la commande
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $orderStatus = 'pending';
            $paymentMethod = $_POST['payment_method'];
            $shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 'standard';

            $stmt = $db->prepare("
                INSERT INTO orders (
                    user_id, order_status, total_amount,
                    shipping_address, shipping_city, shipping_postal_code, shipping_country,
                    payment_method, shipping_method
                ) VALUES (
                    :user_id, :order_status, :total_amount,
                    :shipping_address, :shipping_city, :shipping_postal_code, :shipping_country,
                    :payment_method, :shipping_method
                )
            ");

            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':order_status', $orderStatus);
            $stmt->bindParam(':total_amount', $total);
            $stmt->bindParam(':shipping_address', $_POST['address']);
            $stmt->bindParam(':shipping_city', $_POST['city']);
            $stmt->bindParam(':shipping_postal_code', $_POST['postal_code']);
            $stmt->bindParam(':shipping_country', $_POST['country']);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':shipping_method', $shippingMethod);

            $stmt->execute();

            $orderId = $db->lastInsertId();

            // Enregistrer les articles de la commande
            foreach ($cartItems as $item) {
                $price = $item['discount_price'] ? $item['discount_price'] : $item['price'];

                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, sneaker_id, size_id, quantity, price)
                    VALUES (:order_id, :sneaker_id, :size_id, :quantity, :price)
                ");

                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':sneaker_id', $item['sneaker_id']);
                $stmt->bindParam(':size_id', $item['size_id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $price);

                $stmt->execute();

                // Mettre à jour le stock
                $stmt = $db->prepare("
                    UPDATE sneaker_sizes
                    SET stock_quantity = stock_quantity - :quantity
                    WHERE sneaker_id = :sneaker_id AND size_id = :size_id
                ");

                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':sneaker_id', $item['sneaker_id']);
                $stmt->bindParam(':size_id', $item['size_id']);

                $stmt->execute();
            }

            // Vider le panier
            $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = :cart_id");
            $stmt->bindParam(':cart_id', $cartId);
            $stmt->execute();

            // Rediriger vers la page de confirmation
            $_SESSION['order_id'] = $orderId;
            header('Location: order-confirmation.php');
            exit();

        } catch (PDOException $e) {
            $error_message = "Une erreur est survenue lors du traitement de votre commande. Veuillez réessayer plus tard.";
            error_log("Erreur lors du traitement de la commande : " . $e->getMessage());
        }
    }
}

// Récupérer les informations de l'utilisateur s'il est connecté
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("
        SELECT * FROM users
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch();
}

// Titre et description de la page
$page_title = "Paiement - Bander-Sneakers";
$page_description = "Finaliser votre commande sur Bander-Sneakers. Entrez vos informations de livraison et de paiement.";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="cart.php">Panier</a></li>
            <li class="active">Paiement</li>
        </ul>
    </div>
</div>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <div class="checkout-header">
            <h1 class="checkout-title">Finaliser votre commande</h1>
            <div class="checkout-steps">
                <div class="checkout-step completed">
                    <div class="step-number"><i class="fas fa-shopping-cart"></i></div>
                    <div class="step-label">Panier</div>
                </div>
                <div class="step-connector"></div>
                <div class="checkout-step active">
                    <div class="step-number"><i class="fas fa-credit-card"></i></div>
                    <div class="step-label">Paiement</div>
                </div>
                <div class="step-connector"></div>
                <div class="checkout-step">
                    <div class="step-number"><i class="fas fa-check"></i></div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="checkout-form-container">
                <form action="checkout.php" method="POST" class="checkout-form">
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h2>Informations personnelles</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">Prénom <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="first_name" name="first_name" value="<?= $user ? htmlspecialchars($user['first_name']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Nom <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="last_name" name="last_name" value="<?= $user ? htmlspecialchars($user['last_name']) : '' ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" id="email" name="email" value="<?= $user ? htmlspecialchars($user['email']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="tel" id="phone" name="phone" value="<?= $user ? htmlspecialchars($user['phone']) : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h2>Adresse de livraison</h2>
                        </div>
                        <div class="form-group">
                            <label for="address">Adresse <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-home input-icon"></i>
                                <input type="text" id="address" name="address" value="<?= $user ? htmlspecialchars($user['address']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">Ville <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-city input-icon"></i>
                                    <input type="text" id="city" name="city" value="<?= $user ? htmlspecialchars($user['city']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Code postal <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-mail-bulk input-icon"></i>
                                    <input type="text" id="postal_code" name="postal_code" value="<?= $user ? htmlspecialchars($user['postal_code']) : '' ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="country">Pays <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-globe input-icon"></i>
                                <select id="country" name="country" required>
                                    <option value="">Sélectionnez un pays</option>
                                    <option value="France" <?= $user && $user['country'] == 'France' ? 'selected' : '' ?>>France</option>
                                    <option value="Belgique" <?= $user && $user['country'] == 'Belgique' ? 'selected' : '' ?>>Belgique</option>
                                    <option value="Suisse" <?= $user && $user['country'] == 'Suisse' ? 'selected' : '' ?>>Suisse</option>
                                    <option value="Luxembourg" <?= $user && $user['country'] == 'Luxembourg' ? 'selected' : '' ?>>Luxembourg</option>
                                    <option value="Canada" <?= $user && $user['country'] == 'Canada' ? 'selected' : '' ?>>Canada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h2>Mode de livraison</h2>
                        </div>
                        <div class="shipping-options">
                            <div class="shipping-option">
                                <input type="radio" id="shipping_standard" name="shipping_method" value="standard" checked>
                                <label for="shipping_standard" class="shipping-card">
                                    <div class="shipping-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="shipping-details">
                                        <span class="shipping-name">Livraison standard</span>
                                        <span class="shipping-info">Livraison sous 3-5 jours ouvrés</span>
                                    </div>
                                    <div class="shipping-price"><?= $shipping > 0 ? formatPrice($shipping) : 'Gratuit' ?></div>
                                </label>
                            </div>
                            <div class="shipping-option">
                                <input type="radio" id="shipping_express" name="shipping_method" value="express">
                                <label for="shipping_express" class="shipping-card">
                                    <div class="shipping-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="shipping-details">
                                        <span class="shipping-name">Livraison express</span>
                                        <span class="shipping-info">Livraison sous 24-48h</span>
                                    </div>
                                    <div class="shipping-price"><?= formatPrice(2.99) ?></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h2>Mode de paiement</h2>
                        </div>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="payment_card" name="payment_method" value="card" checked>
                                <label for="payment_card" class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-name">Carte bancaire</div>
                                    <div class="card-types">
                                        <i class="fab fa-cc-visa"></i>
                                        <i class="fab fa-cc-mastercard"></i>
                                        <i class="fab fa-cc-amex"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="payment_paypal" name="payment_method" value="paypal">
                                <label for="payment_paypal" class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                    <div class="payment-name">PayPal</div>
                                    <div class="card-types">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="payment-details" id="card_details">
                            <div class="form-group">
                                <label for="card_number">Numéro de carte</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-credit-card input-icon"></i>
                                    <input type="text" id="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Date d'expiration</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="text" id="expiry_date" placeholder="MM/AA">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="text" id="cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="card_name">Nom sur la carte</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="card_name" placeholder="John Doe">
                                </div>
                            </div>
                            <div class="payment-security">
                                <div class="security-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <p class="payment-info">Les informations de votre carte ne sont pas enregistrées. Ce site utilise un système de paiement sécurisé conforme aux normes PCI DSS.</p>
                            </div>
                        </div>

                        <div class="payment-details" id="paypal_details" style="display: none;">
                            <div class="paypal-info">
                                <div class="paypal-icon">
                                    <i class="fab fa-paypal"></i>
                                </div>
                                <p>Vous serez redirigé vers PayPal pour finaliser votre paiement en toute sécurité.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour au panier
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg checkout-btn">
                            <i class="fas fa-lock"></i> Finaliser la commande
                        </button>
                    </div>
                </form>
            </div>

            <div class="checkout-summary">
                <div class="summary-header">
                    <h2>Récapitulatif</h2>
                    <span class="items-count"><?= count($cartItems) ?> article<?= count($cartItems) > 1 ? 's' : '' ?></span>
                </div>

                <div class="order-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <?php if ($item['image_url']): ?>
                                    <img src="assets/images/sneakers/<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['sneaker_name']) ?>">
                                <?php else: ?>
                                    <div class="no-image"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <div class="item-quantity"><?= $item['quantity'] ?></div>
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['sneaker_name']) ?></h3>
                                <div class="item-meta">
                                    <span class="item-brand"><?= htmlspecialchars($item['brand_name']) ?></span>
                                    <span class="item-size">Taille: <?= $item['size_value'] ?> (<?= $item['size_type'] ?>)</span>
                                </div>
                            </div>
                            <div class="item-price">
                                <?php
                                $itemPrice = $item['discount_price'] ? $item['discount_price'] : $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                                echo formatPrice($itemTotal);
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-totals">
                    <div class="total-row">
                        <span>Sous-total:</span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Frais de livraison:</span>
                        <span id="shipping-cost"><?= $shipping > 0 ? formatPrice($shipping) : 'Gratuit' ?></span>
                    </div>
                    <?php if ($subtotal >= 100): ?>
                    <div class="free-shipping-notice">
                        <i class="fas fa-truck"></i> Livraison gratuite
                    </div>
                    <?php else: ?>
                    <div class="free-shipping-progress">
                        <div class="progress-text">
                            <i class="fas fa-truck"></i>
                            Il vous manque <?= formatPrice(100 - $subtotal) ?> pour bénéficier de la livraison gratuite
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= min(100, ($subtotal / 100) * 100) ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span id="order-total"><?= formatPrice($total) ?></span>
                    </div>
                </div>

                <div class="checkout-guarantees">
                    <div class="guarantee-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Paiement 100% sécurisé</span>
                    </div>
                    <div class="guarantee-item">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Retours gratuits sous 30 jours</span>
                    </div>
                    <div class="guarantee-item">
                        <i class="fas fa-headset"></i>
                        <span>Support client disponible 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle payment details
        const paymentCard = document.getElementById('payment_card');
        const paymentPaypal = document.getElementById('payment_paypal');
        const cardDetails = document.getElementById('card_details');
        const paypalDetails = document.getElementById('paypal_details');

        paymentCard.addEventListener('change', function() {
            if (this.checked) {
                cardDetails.style.display = 'block';
                paypalDetails.style.display = 'none';
            }
        });

        paymentPaypal.addEventListener('change', function() {
            if (this.checked) {
                cardDetails.style.display = 'none';
                paypalDetails.style.display = 'block';
            }
        });

        // Update shipping cost and order total when changing shipping method
        const shippingStandard = document.getElementById('shipping_standard');
        const shippingExpress = document.getElementById('shipping_express');
        const shippingCost = document.getElementById('shipping-cost');
        const orderTotal = document.getElementById('order-total');

        const subtotal = <?= $subtotal ?>;
        const standardShipping = <?= $shipping ?>;
        const expressShipping = 2.99;

        function formatPrice(price) {
            return price.toFixed(2).replace('.', ',') + ' €';
        }

        function updateTotal() {
            let shipping = shippingStandard.checked ? standardShipping : expressShipping;
            let total = subtotal + shipping;

            shippingCost.textContent = shipping > 0 ? formatPrice(shipping) : 'Gratuit';
            orderTotal.textContent = formatPrice(total);
        }

        shippingStandard.addEventListener('change', updateTotal);
        shippingExpress.addEventListener('change', updateTotal);
    });
</script>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
