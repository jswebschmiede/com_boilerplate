# Boilerplate - Joomla Component

## Description

Boilerplate is a base component for Joomla, serving as a starting point for developing custom components. It provides a pre-configured structure and integrates modern development tools for efficient Joomla extension development.

## Features

-   Pre-configured Webpack setup for efficient asset management
-   Integration of Tailwind CSS for modern, responsive styling (optional)
-   Automated build processes for development and production
-   Progress display during the build process
-   Automatic creation of ZIP archives for easy installation
-   Automatic copying of files to your Joomla installation
-   Composer support (if needed uncomment the autoload.php in the services/provider.php file)
-   Category support (if not needed, look at https://github.com/jswebschmiede/com_simpleboilerplate)

## Prerequisites

-   Node.js (version 14 or higher)
-   pnpm (can be installed globally with `npm install -g pnpm`)
-   Joomla 5.x or higher (tested with Joomla 5.0)
-   PHP 8.3 or higher (tested with PHP 8.3)

## Installation

1. Clone the repository:

    ```
    git clone https://github.com/jswebschmiede/com_boilerplate.git
    ```

2. Navigate to the project directory:

    ```
    cd com_boilerplate
    ```

3. Install dependencies:

    ```
    pnpm install
    ```

4. Install Composer dependencies:

    ```
    composer install
    ```

## Usage

### Development Mode

To work in development mode and benefit from automatic reloading and copying the files to your Joomla installation:

-   install the component in Joomla (see Production Mode)
-   configure the `webpack.config.js` file with the path to your Joomla installation (default is `../../joomla`)
-   folder structure should look like this. You can change the names of the folders, important is only the structur itself.

```
joomla_dev/
    - joomla/
    - joomla_components/
        - com_boilerplate/
```

-   start the development server:

```
pnpm run dev
```

### Production Mode

To create a production-ready version of your component:

```
pnpm run build
```

This creates an optimized version of the component and packages it into a ZIP file for installation in Joomla.

## Project Structure

-   `src/`: Component source code
    -   `administrator/`: Administrator area of the component
    -   `components/`: Site area of the component
    -   `media/`: Assets such as JavaScript and CSS
-   `dist/`: Compiled and optimized files (after build)
-   `webpack.config.js`: Webpack configuration
-   `tailwind.config.js`: Tailwind CSS configuration
-   `composer.json`: Composer configuration
-   `package.json`: Project dependencies and scripts

## Customization

You can customize the component by editing the files in the `src/` directory. The main customization points are:

-   replace all occurences of `com_boilerplate` with your component name, don't forget to change the name in the `package.json` file, the `webpack.config.js` file and the `composer.json` file too
-   replace all occurences of `Boilerplate` and `boilerplate` with your component name

## Contributing

Contributions are welcome! Please create a pull request or open an issue for suggestions and bug reports.

## License

MIT License; see LICENSE.txt
