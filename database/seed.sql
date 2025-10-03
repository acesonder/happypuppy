-- HappyPuppy Seed Data
-- Sample data for development

USE happypuppy;

-- Insert admin user (password: admin123)
-- Password hash using PHP: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (email, password, name, phone, street, city, zip_code, role) VALUES
('admin@happypuppy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', '555-0001', '123 Admin St', 'Seattle', '98101', 'admin'),
('user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', '555-0002', '456 User Ave', 'Seattle', '98102', 'user');

-- Insert sample products
INSERT INTO products (name, description, category, quantity, unit, safety_info, is_available, image) VALUES
('Naloxone Nasal Spray', 'Life-saving overdose reversal medication. Essential for opioid harm reduction.', 'Emergency', 50, 'doses', 'Administer into nostril if someone is unresponsive or not breathing properly. Call 911 immediately. May need multiple doses.', TRUE, 'naloxone.jpg'),
('Fentanyl Test Strips', 'Detect fentanyl in substances before use. Quick and accurate results.', 'Testing', 200, 'strips', 'Dissolve small amount of substance in water, dip strip for 15 seconds, wait 2 minutes for results. Two lines = negative, one line = positive.', TRUE, 'test-strips.jpg'),
('Clean Needles 10-pack', 'Sterile syringes to prevent infection and disease transmission.', 'Injection', 100, 'packs', 'Use new needle each time. Never share needles. Dispose in sharps container. Clean injection site with alcohol pad first.', TRUE, 'needles.jpg'),
('Alcohol Prep Pads', 'Sterile alcohol wipes for cleaning injection sites and preventing infection.', 'Hygiene', 75, 'packs', 'Wipe injection site in circular motion before use. Let dry completely before injecting.', TRUE, 'alcohol-pads.jpg'),
('Sharps Containers', 'Safe disposal containers for used needles and syringes.', 'Safety', 30, 'containers', 'Never recap needles. Drop used sharps directly into container. When 3/4 full, seal and dispose properly.', TRUE, 'sharps-container.jpg'),
('Tourniquets', 'Medical-grade tourniquets for safer injection practices.', 'Injection', 80, 'pieces', 'Apply above injection site, never leave on more than 2 minutes. Release immediately after injection.', TRUE, 'tourniquet.jpg'),
('Vitamin C/Ascorbic Acid', 'Safer acidifier for preparing crack cocaine for injection.', 'Preparation', 150, 'packets', 'Dissolve substance first, then add vitamin C. Safer alternative to lemon juice or vinegar.', TRUE, 'vitamin-c.jpg'),
('Sterile Water Ampules', 'Sterile water for preparing injections safely.', 'Preparation', 120, 'ampules', 'Use fresh ampule each time. Never reuse or share water. Safer than tap water which may contain bacteria.', TRUE, 'sterile-water.jpg'),
('Condoms 12-pack', 'Protection for safer sex practices.', 'Prevention', 60, 'packs', 'Check expiration date. Store in cool, dry place. Use new condom each time.', TRUE, 'condoms.jpg'),
('Safe Smoking Kits', 'Clean pipes and stems for safer smoking practices.', 'Smoking', 40, 'kits', 'Use your own pipe. Clean between uses. Avoid sharing mouthpieces to prevent disease transmission.', TRUE, 'smoking-kit.jpg');
