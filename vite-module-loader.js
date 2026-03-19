import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function collectModuleAssetsPaths(paths, modulesPath) {
  modulesPath = path.join(__dirname, modulesPath);

  const moduleStatusesPath = path.join(__dirname, 'modules_statuses.json');

  try {
    // Read module_statuses.json
    const moduleStatusesContent = await fs.readFile(moduleStatusesPath, 'utf-8');
    const moduleStatuses = JSON.parse(moduleStatusesContent);

    // Read module directories
    const moduleDirectories = await fs.readdir(modulesPath);

    for (const moduleDir of moduleDirectories) {
      if (moduleDir === '.DS_Store') {
        // Skip .DS_Store directory
        continue;
      }

      // Check if the module is enabled (status is true)
      if (moduleStatuses[moduleDir] === true) {
        // Prefer a simple JSON manifest so we don't have to import/execute module vite.config.js.
        // File format:
        // { "paths": ["Modules/Setting/resources/frontend/admin/main.tsx", ...] }
        const pathsJson = path.join(modulesPath, moduleDir, 'vite.paths.json');

        try {
          const raw = await fs.readFile(pathsJson, 'utf-8');
          const parsed = JSON.parse(raw);

          if (parsed?.paths && Array.isArray(parsed.paths)) {
            paths.push(...parsed.paths);
          }
        } catch (error) {
          // vite.paths.json does not exist or invalid, skip this module
        }
      }
    }
  } catch (error) {
    console.error(`Error reading module statuses or module configurations: ${error}`);
  }

  return paths;
}

export default collectModuleAssetsPaths;
