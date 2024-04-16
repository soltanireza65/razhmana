<?php
global $Settings;
global $lang;
// Function to format file size in KB, MB, or GB
use MJ\Utils\Utils;

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

// Path to your directory
$dir = getcwd() . '/uploads/medias';

// Parameters for pagination and search
$length = $_POST['length'];
$start = $_POST['start'];
$searchValue = $_POST['search']['value'];

// Required for DataTables
$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => 0,
    "recordsFiltered" => 0,
    "data" => array()
);

// Fetching files from the directory
$fileData = array();
if ($handle = opendir($dir)) {
    $counter  =1;
    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {
            $filePath = $dir . '/' . $entry;
            if (is_readable($filePath)) {
                $fileSize = formatSizeUnits(filesize($filePath)); // Get and format file size

               
                $filename = ' <td>
                             <i data-feather="file-text" class="icon-dual"></i>
                             <span class="ms-2 fw-normal">
                                <a href="javascript: void(0);" class="text-reset">
                                    <bdi>' . $entry . '</bdi>
                                </a>
                             </span>
                           </td>';

                $fileSize ='<td>
                                <bdi>'.$fileSize.'</bdi>
                            </td>';
                $fileCreationTime =' <td>
                                         <p class="mb-0">
                                             <bdi>'.Utils::getTimeCountry($Settings['time_format'], fileatime( $filePath)).'</bdi>
                                         </p>
                                         <span class="font-12"><bdi>'.Utils::getTimeCountry($Settings['time_format'], fileatime( $filePath)).'</bdi></span>
                                     </td>';
                $action ='  <td>
                                <a class="action-icon copyyyyyyyyyyyy"
                                   data-mj-copy="true"
                                   data-bs-toggle="tooltip"
                                   data-bs-trigger="click"
                                   data-bs-placement="top"
                                   title="'.$lang['copied'].'"
                                   data-mj-src="https://ntirapp.com/uploads/medias/'.$entry.'"
                                   href="javascript:void(0);">
                                    <i class="mdi mdi-content-copy text-muted vertical-middle"></i>
                                </a>

                                <a class="action-icon show "
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="'.$lang['show_detail'].'"
                                   href="https://ntirapp.com/uploads/medias/'.$entry.'"
                                >
                                    <style>
                                        #popup {
                                            width: 20px;
                                            pointer-events: none;
                                            position: fixed;
                                            top: 50%;
                                            left: 50%;
                                            transform: translate(-50%,-50%);
                                        }

                                        #popup img {
                                            width: 300px;
                                            position: absolute;
                                            right: 50%;
                                            transform: translateX(50%);
                                            border-radius: 20px;
                                            box-shadow: 0 0 30px rgba(73, 73, 73, 0.6);
                                        }
                                    </style>

                                    <div class="popup" id="popup">
                                        <img src="#" class="d-none" id="popup-image">
                                    </div>
                                    <i class="mdi mdi-eye text-muted vertical-middle"></i>
                                </a>
                                <a class="action-icon"
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="'.$lang['download'].'"
                                   href="https://ntirapp.com/uploads/medias/'.$entry.'"
                                   download="">
                                    <i class="mdi mdi-download text-muted vertical-middle"></i>
                                </a>
                                <a class="action-icon delete"
                                   href="javascript: void(0);"
                                   data-id="'.$counter.'"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="'.$lang['delete'].'"
                                   data-src="https://ntirapp.com/uploads/medias/'.$entry.'">
                                    <i class="mdi mdi-delete text-muted vertical-middle"></i>
                                </a>
                            </td>';
                $fileData[] = array($entry, $fileCreationTime, $fileSize ,$action); // Add file name, creation time, and formatted size
            }
        }
    }
    closedir($handle);
}

// Implementing server-side searching
$filteredData = array();
if (!empty($searchValue)) {
    foreach ($fileData as $file) {
        if (stripos($file[0], $searchValue) !== false) {
            $filteredData[] = $file;
        }
    }
} else {
    $filteredData = $fileData;
}
$filteredData = array_reverse($filteredData);
// Implementing pagination
$paginatedData = array_slice($filteredData, $start, $length);

// Set the total records count
$output['recordsTotal'] = count($fileData);
$output['recordsFiltered'] = count($filteredData);

// Format data for the current page
$output['data'] = $paginatedData;

// Encode and return JSON
echo json_encode($output);
?>