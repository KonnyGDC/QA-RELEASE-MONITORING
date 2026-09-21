const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const html = fs.readFileSync(path.join(root, 'testingv2.html'), 'utf8');
const sampleMatch = html.match(/const imageSampleData = \[([\s\S]*?)\];/);
if (!sampleMatch) throw new Error('imageSampleData not found');
const tickets = eval('[' + sampleMatch[1] + ']');

function esc(s) {
  return String(s).replace(/\\/g, '\\\\').replace(/'/g, "''");
}

function subUser(name, role) {
  return `(SELECT id FROM \`user\` WHERE name = '${esc(name)}' AND role = '${role}' LIMIT 1)`;
}

function subReleaseStatus(name) {
  return `(SELECT id FROM release_status WHERE name = '${esc(name)}' LIMIT 1)`;
}

function parseCreatedTime(str) {
  if (!str) return '2020-01-01 00:00:00';
  const [datePart, timePart = '00:00'] = String(str).trim().split(' ');
  const parts = datePart.split('-');
  if (parts.length !== 3) return '2020-01-01 00:00:00';
  let y, mo, d;
  if (parts[0].length === 4) {
    y = parts[0];
    mo = parts[1];
    d = parts[2];
  } else {
    mo = parts[0];
    d = parts[1];
    y = parts[2];
  }
  const [hh = '0', mm = '0'] = timePart.split(':');
  return `${y}-${mo.padStart(2, '0')}-${d.padStart(2, '0')} ${hh.padStart(2, '0')}:${mm.padStart(2, '0')}:00`;
}

const inserts = tickets.map((t) => {
  const ownerName = t['Change Owner'];
  const qaName = t['Assigned QA'];
  let qaSql = 'NULL';
  if (qaName && qaName !== 'Not Assigned') {
    qaSql = subUser(qaName, 'QA');
  }
  const created = parseCreatedTime(t['Created Time']);
  return `INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
SELECT (SELECT id FROM new_sprint WHERE name = 'Sprint 10' LIMIT 1), '${esc(t['Change ID'])}', '${esc(t.Title)}', ${subUser(ownerName, 'Developer')}, ${qaSql}, '${esc(t['Change Stage'])}', '${esc(t['Change Status'])}', '${esc(t['Change Type'])}', '${esc(created)}', ${subReleaseStatus(t['Release Status'])}
ON DUPLICATE KEY UPDATE change_id = qa_data.change_id;`;
});

const header = `-- QA Release Monitoring — demo seed (safe to re-run)
-- Tables: release_status, new_sprint, user, qa_data
-- Run AFTER database/schema.sql and database/migrate.sql

SET NAMES utf8mb4;

INSERT INTO release_status (name) VALUES
('Released'),
('For release'),
('Not in release')
ON DUPLICATE KEY UPDATE name = release_status.name;

INSERT INTO new_sprint (name) VALUES ('Sprint 10')
ON DUPLICATE KEY UPDATE name = new_sprint.name;

INSERT INTO \`user\` (name, role) VALUES
('Andrew C. Baldonado', 'Developer'),
('Ayn Mathew A. Astrera', 'Developer'),
('Bryan E. Berza', 'Developer'),
('Cene Vincent A. Soriano', 'Developer'),
('Charm Dominic N. Perez', 'Developer'),
('Earl Rhayan D. Padua', 'Developer'),
('Francis Alfonso Bandelaria', 'Developer'),
('Jameson T. Carigao', 'Developer'),
('John Ernie E. Angeles', 'Developer'),
('John Kylle Eries A. Carreon', 'Developer'),
('Karl Ghian C. Mercado', 'Developer'),
('Nario S. Albos', 'Developer'),
('Raven B. Bautista', 'Developer'),
('Ric M. Gregorio', 'Developer'),
('Rick Rosell O. David', 'Developer'),
('Rona Jean B. Castro', 'Developer'),
('Vincent C. Baylon', 'Developer'),
('Francis Alfonso Bandelaria', 'QA'),
('Mark Anthony A. Udarbe', 'QA'),
('Mryk Howell D. David', 'QA')
ON DUPLICATE KEY UPDATE name = \`user\`.name;

`;

const seedBody = header + inserts.join('\n\n') + '\n';
fs.writeFileSync(path.join(root, 'database', 'seed.sql'), seedBody);
console.log('Wrote database/seed.sql', inserts.length, 'tickets');

const combinedHeader = `-- QA Release Monitoring — one-file SAFE install (phpMyAdmin)
-- Does NOT drop tables. Re-running keeps existing qa_data rows.
-- Same as: database/schema.sql + database/migrate.sql + database/seed.sql

`;
const schema = fs.readFileSync(path.join(root, 'database', 'schema.sql'), 'utf8');
const migrate = fs.readFileSync(path.join(root, 'database', 'migrate.sql'), 'utf8');
fs.writeFileSync(
  path.join(root, 'database.sql'),
  combinedHeader + schema + '\n\n' + migrate + '\n\n' + seedBody
);
console.log('Wrote database.sql (combined safe install)');
