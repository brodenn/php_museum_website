<?php

/**
 * Generates an HTML table representation from a query result set.
 *
 * @param array $queryResults The results of a database query.
 * @param string|null $linkPrefix Optional prefix for links on key column values.
 * @param int $keyColumnIndex Index of the key column for creating links.
 * @return string HTML markup of the table.
 */
function renderTable(array $queryResults, ?string $linkPrefix = null, int $keyColumnIndex = 0): string
{
    if (empty($queryResults)) {
        return "<p>No data available.</p>";
    }

    $tableHTML = '<div class="scrollable-table-container"><table><thead><tr>';

    // Add Actions header as the first column
    $tableHTML .= "<th>Actions</th>";

    foreach (array_keys($queryResults[0]) as $columnName) {
        $tableHTML .= "<th>$columnName</th>";
    }

    $tableHTML .= '</tr></thead><tbody>';

    foreach ($queryResults as $row) {
        $tableHTML .= '<tr>';

        // Retrieve the key value for the current row
        $keyValue = htmlspecialchars($row[array_keys($row)[$keyColumnIndex]] ?? '', ENT_QUOTES, 'UTF-8');

        $editUrl = htmlspecialchars($linkPrefix . "&action=edit&key=" . $keyValue);
        $deleteUrl = htmlspecialchars($linkPrefix . "&action=delete&key=" . $keyValue);
        $tableHTML .= "<td><a href='$editUrl' class='btn btn-edit'>Edit</a> <a href='$deleteUrl' class='btn btn-delete' onclick='return confirm(\"Are you sure?\");'>Delete</a></td>";

        foreach ($row as $columnName => $value) {
            $safeValue = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
            $valueHTML = "<div class='scrollable-column'>$safeValue</div>";
            $tableHTML .= "<td>$valueHTML</td>";
        }

        $tableHTML .= '</tr>';
    }

    $tableHTML .= '</tbody></table></div>';
    return $tableHTML;
}

/**
 * Renders a grid layout for database roads as HTML.
 *
 * @param array $roadsData Database roads to display.
 * @return string HTML content grid.
 */
function renderRoadsGrid(array $roadsData): string
{
    if (empty($roadsData)) {
        return "<p>No roads to display.</p>";
    }

    $gridHTML = '<div class="roads-grid">';
    foreach ($roadsData as $road) {
        $imageSrc = htmlspecialchars($road['image1']);
        $roadTitle = htmlspecialchars($road['title']);
        $imageAltText = htmlspecialchars($road['image1Alt']);
        $imageCaption = htmlspecialchars($road['image1Text']);

        $gridHTML .= "<div class='grid-item'>";
        $gridHTML .= "<h3>$roadTitle</h3>";
        $gridHTML .= "<figure><img src='img/250/$imageSrc' alt='$imageAltText'><figcaption>$imageCaption</figcaption></figure>";
        $gridHTML .= "</div>";
    }
    $gridHTML .= '</div>';
    return $gridHTML;
}



/**
 * Given columns and search string
 * produce filter string for WHERE-part of the SELECT command
 *
 * @param array $columnNames
 * @param string $search
 * @return string $filter
 */

/**
 * Generates an HTML table representation of the given records, optionally including actions (edit/delete) for each row.
 *
 * @param array $records An array of associative arrays representing the records to be displayed in the HTML table.
 * @param string $tableName The name of the table from which the records were fetched. Used to construct action URLs.
 * @param int $keyColIndex The index of the primary key column in the records array. Used for action links.
 * @param bool $addActions If true, edit and delete action links are included for each row in the table.
 * @return string The generated HTML table as a string. Returns a message if no records are found.
 */
function generateHtmlTable($records, $tableName, $keyColIndex, $addActions)
{
    if (empty($records)) {
        return "<p>No records found.</p>";
    }

    $htmlTable = "<table><tr>";
    foreach (array_keys($records[0]) as $header) {
        $htmlTable .= "<th>" . htmlspecialchars($header) . "</th>";
    }
    if ($addActions) {
        $htmlTable .= "<th>Actions</th>";
    }
    $htmlTable .= "</tr>";

    foreach ($records as $row) {
        $htmlTable .= "<tr>";
        foreach ($row as $value) {
            $htmlTable .= "<td>" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "</td>";
        }
        if ($addActions) {
            $primaryKeyValue = htmlspecialchars($row[array_keys($row)[$keyColIndex]], ENT_QUOTES, 'UTF-8');
            $editUrl = "edit.php?action=edit&table=$tableName&key=$primaryKeyValue";
            $deleteUrl = "delete.php?action=delete&table=$tableName&key=$primaryKeyValue";
            $htmlTable .= "<td><a href='$editUrl'>Edit</a> | <a href='$deleteUrl' onclick='return confirm(\"Are you sure?\");'>Delete</a></td>";
        }
        $htmlTable .= "</tr>";
    }
    $htmlTable .= "</table>";
    return $htmlTable;
}


function renderDynamicForm($action, $table, $record = null, $excludeColumns = [], $primaryKeyColumn = '', $requiredFields = [])
{
    // Initialize form HTML
    $formHTML = "<form method='POST' action=''>\n";
    $formHTML .= "<input type='hidden' name='action' value='{$action}'>\n";
    $formHTML .= "<input type='hidden' name='table' value='{$table}'>\n";

    // Include primary key as hidden field for edit actions
    if ($action === 'edit' && $primaryKeyColumn && isset($record[$primaryKeyColumn])) {
        $primaryKeyValue = htmlspecialchars($record[$primaryKeyColumn], ENT_QUOTES, 'UTF-8');
        $formHTML .= "<input type='hidden' name='{$primaryKeyColumn}' value='{$primaryKeyValue}'>\n";
    }

    // Iterate through each column to generate form fields, excluding specified columns
    foreach ($record as $column => $value) {
        if (in_array($column, $excludeColumns)) {
            continue;
        }
        $isRequired = in_array($column, $requiredFields) ? 'required' : '';
        $safeValue = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); // Handle null values to avoid deprecated warnings
        $formLabel = ucfirst(str_replace('_', ' ', $column)); // Improving readability by replacing underscores and capitalizing
        $formHTML .= "<div class='form-group'>\n";
        $formHTML .= "<label for='{$column}'>{$formLabel}:</label>\n";
        $formHTML .= "<input type='text' id='{$column}' name='{$column}' value='{$safeValue}' {$isRequired}>\n";
        $formHTML .= "</div>\n";
    }

    // Add submit button
    $formHTML .= "<div class='form-group'>\n";
    $formHTML .= "<button type='submit' name='submit'>" . ucfirst($action) . " Record</button>\n";
    $formHTML .= "</div>\n";
    $formHTML .= "</form>\n";

    return $formHTML;
}


/**
 * Format given row as HTML.
 * It is a helper function for getPhpContentFromDB to format the fetched data as HTML content,
 * including handling images and navigation links to previous and next items.
 *
 * The function aims to reduce cyclomatic complexity in getPhpContentFromDB by offloading the
 * content formatting logic. This helps maintain readability and manageability, especially when dealing
 * with complex content structures.
 *
 * @param array $row The database row containing content data.
 * @param string $previousItemLink HTML anchor tag for the previous item.
 * @param string $nextItemLink HTML anchor tag for the next item.
 * @return string Formatted HTML content.
 */
function formatRowAsHtml(array $row, string $previousItemLink, string $nextItemLink): string
{
    // Initialize image tags as empty strings.
    $img1Tag = '';
    // Check if the first image exists and create its HTML tag.
    if (!is_null($row["image1"])) {
        $img1Tag = <<<IMG
            <div class="content-center">
                {$row["gps"]}
                <figure>
                    <img src="../img/500/{$row["image1"]}" alt="{$row["image1Alt"]}">
                    <figcaption>{$row["image1Text"]}</figcaption>
                </figure>
            </div>
        IMG;
    }

    $img2Tag = '';
    // Check if the second image exists and create its HTML tag.
    if (!is_null($row["image2"])) {
        $img2Tag = <<<IMG
            <figure>
                <img src="../img/500/{$row["image2"]}" alt="{$row["image2Alt"]}">
                <figcaption>{$row["image2Text"]}</figcaption>
            </figure>
        IMG;
    }

    // Construct the HTML content using the provided data and the generated image tags.
    return <<<EOL
        <header class="grid">
            $previousItemLink
            <h1>{$row["title"]}</h1>
            $nextItemLink
        </header>
        $img1Tag
        {$row["data"]}
        $img2Tag
        <p class="author"> Författare: {$row["author"]}</p>
    EOL;
}
