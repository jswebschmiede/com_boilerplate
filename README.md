# Boilerplate - Joomla Component (Work in Progress)

## Description

Boilerplate is a base component for Joomla, serving as a starting point for developing custom components. It provides a pre-configured structure and integrates modern development tools for efficient Joomla extension development.

## Features

-   Pre-configured Webpack setup for efficient asset management
-   Integration of Tailwind CSS for modern, responsive styling
-   Automated build processes for development and production
-   Progress display during the build process
-   Automatic creation of ZIP archives for easy installation

## Prerequisites

-   Node.js (version 14 or higher)
-   pnpm (can be installed globally with `npm install -g pnpm`)
-   Joomla 4.x or higher

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

## Usage

### Development Mode

To work in development mode and benefit from automatic reloading:

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
-   `package.json`: Project dependencies and scripts

## Customization

You can customize the component by editing the files in the `src/` directory. The main customization points are:

-   replace all occurences of `com_boilerplate` with your component name, don't forget to change the name in the `package.json` file and the `webpack.config.js` file too
-   replace all occurences of `Boilerplate` with your component name

## Contributing

Contributions are welcome! Please create a pull request or open an issue for suggestions and bug reports.

## License

MIT License; see LICENSE.txt
