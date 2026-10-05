const fs = require('fs');
const path = require('path');
const os = require('os');
const { execFileSync } = require('child_process');

const siteDir = path.join(__dirname, '..', 'site');
const pages = fs.readdirSync(siteDir).filter((f) => f.endsWith('.html'));

const tmp = path.join(os.tmpdir(), 'sh-inline-check.js');
let blocks = 0;
let failed = 0;

pages.forEach((page) => {
  const html = fs.readFileSync(path.join(siteDir, page), 'utf8');
  const scripts = [...html.matchAll(/<script(?![^>]*\bsrc=)[^>]*>([\s\S]*?)<\/script>/g)].map((m) => m[1]);
  scripts.forEach((code, i) => {
    blocks++;
    fs.writeFileSync(tmp, code, 'utf8');
    try {
      execFileSync(process.execPath, ['--check', tmp], { stdio: 'pipe' });
    } catch (e) {
      failed++;
      console.error(`${page} inline script block ${i + 1} is invalid:\n${(e.stderr || '').toString()}`);
    }
  });
});

if (failed) process.exit(1);
console.log(`OK - ${blocks} inline script block(s) parsed across ${pages.length} pages`);