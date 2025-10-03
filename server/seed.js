const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');
require('dotenv').config();

const User = require('./models/User');
const Product = require('./models/Product');

const seedDatabase = async () => {
  try {
    await mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/happypuppy');
    console.log('Connected to MongoDB');

    // Clear existing data
    await User.deleteMany({});
    await Product.deleteMany({});
    console.log('Cleared existing data');

    // Create admin user
    const adminPassword = await bcrypt.hash('admin123', 10);
    const admin = new User({
      email: 'admin@happypuppy.com',
      password: adminPassword,
      name: 'Admin User',
      phone: '555-0100',
      role: 'admin',
      address: {
        street: '123 Admin St',
        city: 'Admin City',
        zipCode: '12345'
      }
    });
    await admin.save();
    console.log('Admin user created: admin@happypuppy.com / admin123');

    // Create test user
    const userPassword = await bcrypt.hash('user123', 10);
    const user = new User({
      email: 'user@test.com',
      password: userPassword,
      name: 'Test User',
      phone: '555-0200',
      role: 'user',
      address: {
        street: '456 User Ave',
        city: 'User City',
        zipCode: '67890'
      }
    });
    await user.save();
    console.log('Test user created: user@test.com / user123');

    // Create sample products
    const products = [
      {
        name: 'Naloxone Nasal Spray',
        description: 'Emergency opioid overdose reversal medication',
        category: 'Harm Reduction',
        quantity: 50,
        unit: 'doses',
        safetyInfo: 'Use immediately if opioid overdose is suspected. Call 911 after administration. Can be given through clothing.',
        isAvailable: true
      },
      {
        name: 'Fentanyl Test Strips',
        description: 'Test strips to detect fentanyl in substances',
        category: 'Testing Supplies',
        quantity: 200,
        unit: 'strips',
        safetyInfo: 'Use before consuming. Dissolve small sample in water and dip strip for 15 seconds. Wait 5 minutes for results.',
        isAvailable: true
      },
      {
        name: 'Clean Needles (10-pack)',
        description: 'Sterile needles for safer injection',
        category: 'Injection Supplies',
        quantity: 100,
        unit: 'packs',
        safetyInfo: 'Never share needles. Use once and dispose properly in sharps container. Always use new, sterile equipment.',
        isAvailable: true
      },
      {
        name: 'Alcohol Prep Pads (100-pack)',
        description: 'Sterile alcohol wipes for injection site preparation',
        category: 'Injection Supplies',
        quantity: 75,
        unit: 'packs',
        safetyInfo: 'Clean injection site thoroughly before use. Let area dry completely before injection.',
        isAvailable: true
      },
      {
        name: 'Sharps Container',
        description: 'Safe disposal container for used needles',
        category: 'Safety Equipment',
        quantity: 30,
        unit: 'containers',
        safetyInfo: 'Never reach into container. Seal when 3/4 full. Dispose at designated locations.',
        isAvailable: true
      },
      {
        name: 'Tourniquets',
        description: 'Medical-grade tourniquets',
        category: 'Injection Supplies',
        quantity: 80,
        unit: 'pieces',
        safetyInfo: 'Do not leave on for more than 1-2 minutes. Remove before injection.',
        isAvailable: true
      },
      {
        name: 'Vitamin C (Ascorbic Acid)',
        description: 'For safer crack cocaine preparation',
        category: 'Harm Reduction',
        quantity: 150,
        unit: 'packets',
        safetyInfo: 'Use instead of lemon juice or vinegar. Reduces vein damage. Use small amounts.',
        isAvailable: true
      },
      {
        name: 'Sterile Water Ampules',
        description: 'Sterile water for substance preparation',
        category: 'Injection Supplies',
        quantity: 120,
        unit: 'ampules',
        safetyInfo: 'Always use sterile water. Never reuse. Reduces infection risk.',
        isAvailable: true
      },
      {
        name: 'Condoms (12-pack)',
        description: 'Protection for safer sex practices',
        category: 'Sexual Health',
        quantity: 60,
        unit: 'packs',
        safetyInfo: 'Check expiration date. Use water-based lubricant. Store in cool, dry place.',
        isAvailable: true
      },
      {
        name: 'Safe Smoking Kit',
        description: 'Includes glass pipes, screens, and mouthpieces',
        category: 'Smoking Supplies',
        quantity: 40,
        unit: 'kits',
        safetyInfo: 'Never share mouthpieces. Clean pipe regularly. Avoid burnt or cracked pipes.',
        isAvailable: true
      }
    ];

    await Product.insertMany(products);
    console.log(`${products.length} products created`);

    console.log('\nDatabase seeded successfully!');
    console.log('\nLogin credentials:');
    console.log('Admin: admin@happypuppy.com / admin123');
    console.log('User: user@test.com / user123');
    
    process.exit(0);
  } catch (error) {
    console.error('Error seeding database:', error);
    process.exit(1);
  }
};

seedDatabase();
