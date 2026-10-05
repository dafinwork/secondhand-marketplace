const fs = require('fs');
const path = require('path');

const siteDir = path.join(__dirname, '..', 'site');

const raw = process.argv[2] || '';
let base = raw.trim().replace(/\/+$/, '');

if (!/^https?:\/\/[^\s/]+/.test(base)) {
  console.error('Usage: node tools/update-base-url.js https://<owner>.github.io/<repo>');
  process.exit(1);
}

const sitemapPath = path.join(siteDir, 'sitemap.xml');
const robotsPath = path.join(siteDir, 'robots.txt');
const indexPath = path.join(siteDir, 'index.html');

let sitemap = fs.readFileSync(sitemapPath, 'utf8');
const pages = [...sitemap.matchAll(/<loc>[^<]*?([\w-]+\.html)<\/loc>/g)].map((m) => m[1]);
if (!pages.length) {
  console.error('FAIL - no <loc> entries found in site/sitemap.xml');
  process.exit(1);
}
pages.forEach((page) => {
  sitemap = sitemap.replace(new RegExp(`<loc>[^<]*${page}</loc>`), `<loc>${base}/${page}</loc>`);
});
sitemap = sitemap.replace(/^<!-- Placeholder URLs[^\n]*\n/m, '');
fs.writeFileSync(sitemapPath, sitemap);

let robots = fs.readFileSync(robotsPath, 'utf8');
if (/^Sitemap:.*$/m.test(robots)) {
  robots = robots.replace(/^Sitemap:.*$/m, `Sitemap: ${base}/sitemap.xml`);
} else {
  robots = robots.replace(/\s*$/, `\n\nSitemap: ${base}/sitemap.xml\n`);
}
fs.writeFileSync(robotsPath, robots);

let index = fs.readFileSync(indexPath, 'utf8');
index = index.replace(/(<link rel="canonical" href=")[^"]*(")/, `$1${base}/$2`);
fs.writeFileSync(indexPath, index);

console.log(`OK - base URL set to ${base}`);
console.log(`     ${pages.length} sitemap entries, robots.txt, index.html canonical`);