import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Helper function to create directory recursively
const ensureDir = (dir) => {
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
};

// Helper function to copy file
const copyFile = (src, dest) => {
  ensureDir(path.dirname(dest));
  fs.copyFileSync(src, dest);
};

// Helper function to copy directory recursively
const copyDir = (src, dest) => {
  ensureDir(dest);
  const entries = fs.readdirSync(src, { withFileTypes: true });

  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);

    if (entry.isDirectory()) {
      copyDir(srcPath, destPath);
    } else {
      copyFile(srcPath, destPath);
    }
  }
};

try {
  // Remove dist directory if it exists
  const distDir = path.join(__dirname, 'dist');
  if (fs.existsSync(distDir)) {
    fs.rmSync(distDir, { recursive: true });
  }

  // Create dist directory
  ensureDir(distDir);

  // Copy index.html
  copyFile(
    path.join(__dirname, 'index.html'),
    path.join(distDir, 'index.html')
  );

  // Copy favicon.svg
  copyFile(
    path.join(__dirname, 'favicon.svg'),
    path.join(distDir, 'favicon.svg')
  );

  // Copy src directory
  copyDir(
    path.join(__dirname, 'src'),
    path.join(distDir, 'src')
  );

  console.log('✅ Build completed successfully!');
  console.log(`📁 Output directory: ${distDir}`);
} catch (error) {
  console.error('❌ Build failed:', error.message);
  process.exit(1);
}
