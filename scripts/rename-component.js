#!/usr/bin/env node

/* eslint-env node */

/**
 * Replaces boilerplate component placeholders with a project-specific slug
 * and renames files and directories whose names contain "boilerplate".
 */

const { existsSync, readdirSync, readFileSync, renameSync, statSync, writeFileSync } = require('fs');
const { basename, dirname, extname, join, relative } = require('path');

const rootDir = join(__dirname, '..');
const scriptRelativePath = 'scripts/rename-component.js';
const args = process.argv.slice(2);
const isDryRun = args.includes('--dry-run');
const slugArg = args.find((arg) => !arg.startsWith('--'));

const usageMessage = 'Usage: node scripts/rename-component.js <slug> [--dry-run]';

const allowedExtensions = new Set(['.php', '.xml', '.ini', '.json', '.js', '.css', '.md', '.sql']);
const extraContentFiles = new Set(['makefile']);

const skippedDirectories = new Set(['node_modules', '.git', 'dist', 'vendor']);

const reservedSlugs = new Set(['boilerplate', 'com_boilerplate', 'com-boilerplate']);

/**
 * Converts a slug to PascalCase for PHP namespaces.
 *
 * @param {string} value Slug with hyphens and/or underscores
 * @returns {string} PascalCase namespace segment
 */
function slugToNamespace(value) {
    return value
        .split(/[-_]/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

/**
 * Converts a slug to a human-readable title.
 *
 * @param {string} value Slug with hyphens and/or underscores
 * @returns {string} Title with spaces
 */
function slugToTitle(value) {
    return value
        .split(/[-_]/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

/**
 * Strips an optional com_ prefix and normalizes hyphens to underscores.
 *
 * @param {string} value Raw slug argument
 * @returns {string} Component name without com_ prefix
 */
function normalizeSlug(value) {
    return value.replace(/^com_/, '').replaceAll('-', '_');
}

/**
 * Validates the requested component slug.
 *
 * @param {string | undefined} value Requested slug
 * @returns {void}
 */
function validateSlug(value) {
    if (!value) {
        console.error(usageMessage);
        process.exit(1);
    }

    if (!/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/.test(value)) {
        console.error('Slug must contain only lowercase letters, numbers, hyphens, and underscores.');
        console.error(usageMessage);
        process.exit(1);
    }

    const componentName = normalizeSlug(value);

    if (!componentName) {
        console.error('Slug must contain a component name after an optional com_ prefix.');
        console.error(usageMessage);
        process.exit(1);
    }

    if (reservedSlugs.has(value) || reservedSlugs.has(componentName)) {
        console.error('New slug must differ from the boilerplate placeholders.');
        process.exit(1);
    }
}

/**
 * Builds the replacement map from longest boilerplate tokens to the new slug forms.
 *
 * @param {string} componentName Normalized component name (hyphens become underscores)
 * @returns {Record<string, string>} Replacement map
 */
function getReplacements(componentName) {
    const pascal = slugToNamespace(componentName);
    const constant = componentName.toUpperCase();
    const title = slugToTitle(componentName);

    return {
        COM_BOILERPLATE: `COM_${constant}`,
        com_boilerplate: `com_${componentName}`,
        'Component Boilerplate': `Component ${title}`,
        BOILERPLATE: constant,
        Boilerplate: pascal,
        boilerplate: componentName,
    };
}

/**
 * Applies all replacements to a string in a stable order (longer keys first).
 *
 * @param {string} content Original content
 * @param {Record<string, string>} replacements Replacement map
 * @returns {string} Updated content
 */
function applyReplacements(content, replacements) {
    const orderedEntries = Object.entries(replacements).sort(
        ([searchA], [searchB]) => searchB.length - searchA.length
    );

    return orderedEntries.reduce(
        (updatedContent, [search, replacement]) => updatedContent.replaceAll(search, replacement),
        content
    );
}

/**
 * Determines whether a path should be skipped.
 *
 * @param {string} absolutePath Absolute file or directory path
 * @param {string} relativePath POSIX-style path from the project root
 * @returns {boolean} True when the path should be ignored
 */
function shouldSkipPath(absolutePath, relativePath) {
    if (relativePath === scriptRelativePath) {
        return true;
    }

    const baseName = basename(absolutePath);

    return skippedDirectories.has(baseName);
}

/**
 * Determines whether a file should have its contents rewritten.
 *
 * @param {string} file Absolute file path
 * @returns {boolean} True when the file is eligible for content replacement
 */
function shouldProcessContent(file) {
    if (allowedExtensions.has(extname(file))) {
        return true;
    }

    return extraContentFiles.has(basename(file).toLowerCase());
}

/**
 * Recursively collects files and directories under the project root.
 *
 * @param {string} directory Directory to scan
 * @param {string} relativeBase Relative path from the project root
 * @returns {{files: string[], directories: string[]}} Absolute paths
 */
function collectPaths(directory, relativeBase = '') {
    if (!existsSync(directory)) {
        return { files: [], directories: [] };
    }

    const files = [];
    const directories = [];

    for (const entry of readdirSync(directory)) {
        const absolutePath = join(directory, entry);
        const relativePath = relativeBase ? `${relativeBase}/${entry}` : entry;

        if (shouldSkipPath(absolutePath, relativePath)) {
            continue;
        }

        const stats = statSync(absolutePath);

        if (stats.isDirectory()) {
            directories.push(absolutePath);
            const nested = collectPaths(absolutePath, relativePath);
            files.push(...nested.files);
            directories.push(...nested.directories);
            continue;
        }

        if (stats.isFile()) {
            files.push(absolutePath);
        }
    }

    return { files, directories };
}

/**
 * Sorts directory paths so nested folders are processed before their parents.
 *
 * @param {string} pathA First absolute path
 * @param {string} pathB Second absolute path
 * @returns {number} Sort comparator result
 */
function compareDepthDescending(pathA, pathB) {
    return pathB.length - pathA.length;
}

validateSlug(slugArg);

const componentName = normalizeSlug(slugArg);
const replacements = getReplacements(componentName);
const pascal = replacements.Boilerplate;
const { files, directories } = collectPaths(rootDir);
const changedFiles = [];
const renamedFiles = [];
const renamedDirectories = [];

console.log('Component rename values:');
console.log(`Component name: com_${componentName}`);
console.log(`PHP namespace: Joomla\\Component\\${pascal}`);
console.log(`Language prefix: COM_${componentName.toUpperCase()}`);
console.log(`WebAsset: component.${componentName}.site`);
console.log(`Table: #__${componentName}_${componentName}`);
console.log(`Display title: ${slugToTitle(componentName)}`);

for (const file of files) {
    if (!shouldProcessContent(file)) {
        continue;
    }

    const originalContent = readFileSync(file, 'utf8');
    const updatedContent = applyReplacements(originalContent, replacements);

    if (originalContent === updatedContent) {
        continue;
    }

    changedFiles.push(file);

    if (!isDryRun) {
        writeFileSync(file, updatedContent, 'utf8');
    }
}

for (const file of files) {
    const oldName = basename(file);
    const newName = applyReplacements(oldName, replacements);

    if (oldName === newName || !/boilerplate/i.test(oldName)) {
        continue;
    }

    const newPath = join(dirname(file), newName);

    if (existsSync(newPath) && newPath !== file) {
        console.error(`Target file already exists: ${relative(rootDir, newPath)}`);
        process.exit(1);
    }

    renamedFiles.push({ from: file, to: newPath });

    if (!isDryRun) {
        renameSync(file, newPath);
    }
}

const orderedDirectories = [...directories].sort(compareDepthDescending);

for (const directory of orderedDirectories) {
    const oldName = basename(directory);
    const newName = applyReplacements(oldName, replacements);

    if (oldName === newName || !/boilerplate/i.test(oldName)) {
        continue;
    }

    const newPath = join(dirname(directory), newName);

    if (existsSync(newPath) && newPath !== directory) {
        console.error(`Target directory already exists: ${relative(rootDir, newPath)}`);
        process.exit(1);
    }

    renamedDirectories.push({ from: directory, to: newPath });

    if (!isDryRun) {
        renameSync(directory, newPath);
    }
}

if (isDryRun) {
    console.log('Dry run only. No files were changed.');
}

console.log(`Affected files: ${changedFiles.length}`);
for (const file of changedFiles) {
    console.log(relative(rootDir, file));
}

if (renamedFiles.length > 0) {
    console.log(`Renamed files: ${renamedFiles.length}`);
    for (const { from, to } of renamedFiles) {
        console.log(`${relative(rootDir, from)} -> ${relative(rootDir, to)}`);
    }
}

if (renamedDirectories.length > 0) {
    console.log(`Renamed directories: ${renamedDirectories.length}`);
    for (const { from, to } of renamedDirectories) {
        console.log(`${relative(rootDir, from)} -> ${relative(rootDir, to)}`);
    }
}

console.log('');
console.log('Next steps:');
console.log(`- Rename the project folder com_boilerplate -> com_${componentName}`);
console.log('- Update git remotes or workspace paths if needed');
console.log('- Use a singular slug so list views become {slug}s (room -> rooms)');
