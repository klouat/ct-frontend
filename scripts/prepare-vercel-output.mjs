import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import { resolve } from 'node:path';

const root = process.cwd();
const buildDir = resolve(root, 'public', 'build');
const distDir = resolve(root, 'dist');

rmSync(distDir, { recursive: true, force: true });
mkdirSync(distDir, { recursive: true });

if (existsSync(buildDir)) {
  cpSync(buildDir, distDir, { recursive: true });
}
