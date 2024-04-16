<?php


use MJ\Database\DB;
use MJ\Security\Security;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Academy
{


    /**
     * get All Categories
     *
     * @param $status
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function getAllCategories($status = null)
    {
        $response = sendResponse(0, "");

        if (is_null($status)) {
            $sql = "SELECT category_id,category_name,parent_id,category_language,category_status,category_priority FROM `tbl_academy_categories` ORDER BY category_id DESC ;";
            $result = DB::rawQuery($sql, []);
        } else {
            $sql = "SELECT category_id,category_name,parent_id,category_language,category_status,category_priority FROM `tbl_academy_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
            $result = DB::rawQuery($sql, ['category_status' => $status]);
        }

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Insert New Category
     *
     * @param $title
     * @param $language
     * @param $status
     * @param $metaTitle
     * @param $metaDesc
     * @param $schema
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function SetNewCategory($title, $language, $status, $priority, $thumbnailURL, $parent = null, $metaTitle = null, $metaDesc = null, $schema = null)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {

            $sql = 'INSERT INTO `tbl_academy_categories` (`parent_id`,`category_name`,`category_thumbnail`, `category_language`, `category_meta_title`, `category_meta_desc`,
                    `category_schema`, `category_status`,`category_priority`, `category_options`) 
                    VALUES (:parent_id,:category_name,:category_thumbnail,:category_language,:category_meta_title,:category_meta_desc,
                    :category_schema,:category_status,:category_priority,:category_options);';
            $params = [
                'parent_id' => $parent,
                'category_name' => $title,
                'category_thumbnail' => $thumbnailURL,
                'category_language' => $language,
                'category_meta_title' => $metaTitle,
                'category_meta_desc' => $metaDesc,
                'category_schema' => $schema,
                'category_status' => $status,
                'category_priority' => $priority,
                'category_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * Get Academy Category By ID
     *
     * @param $id
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function getCategoryById($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_academy_categories` WHERE category_id=:category_id";
        $params = [
            'category_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }




    /**
     * edit Category By Id
     *
     * @param $category_id
     * @param $title
     * @param $language
     * @param $status
     * @param $metaTitle
     * @param $metaDesc
     * @param $schema
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function editCategoryById($category_id, $title, $language, $status, $priority, $parent = null, $image = null, $metaTitle = null, $metaDesc = null, $schema = null)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryById($category_id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->category_options;
            $imageOld = $res->response[0]->category_thumbnail;
        }

        if (!empty($image)) {
            $imageOld = $image;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));
        }


        $sql = "UPDATE `tbl_academy_categories` SET `category_name`=:category_name,`parent_id`=:parent_id,`category_thumbnail`=:category_thumbnail,`category_language`=:category_language,
                `category_meta_title`=:category_meta_title,`category_meta_desc`=:category_meta_desc,`category_schema`=:category_schema,
                `category_status`=:category_status,`category_priority`=:category_priority,`category_options`=:category_options WHERE category_id=:category_id";
        $params = [
            'category_name' => $title,
            'parent_id' => $parent,
            'category_thumbnail' => $imageOld,
            'category_language' => $language,
            'category_meta_title' => $metaTitle,
            'category_meta_desc' => $metaDesc,
            'category_schema' => $schema,
            'category_status' => $status,
            'category_priority' => $priority,
            'category_options' => json_encode($value),
            'category_id' => $category_id,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * delete Category
     *
     * @param $categoryID
     * @param $replaceID
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function deleteCategory($categoryID, $replaceID)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_academy` SET `category_id`=:replaceID WHERE category_id=:categoryID;
        DELETE FROM `tbl_academy_categories` WHERE `category_id`=:category_id";
        $params = [
            'categoryID' => $categoryID,
            'replaceID' => $replaceID,
            'category_id' => $categoryID
        ];

        $result = DB::transactionQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * get Count Academy By Category ID
     *
     * @param $categoryId
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function getCountAcademyByCategory($categoryId)
    {
        $response = sendResponse(0, "");


        $sql = "SELECT count(*) AS count FROM `tbl_academy` WHERE `category_id`=:category_id";
        $result = DB::rawQuery($sql, ['category_id' => $categoryId]);


        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Set New Academy
     *
     * @param $title
     * @param $body
     * @param $category
     * @param $slug
     * @param $status
     * @param $thumbnail
     * @param $language
     * @param $excerpt
     * @param $metaDesc
     * @param $schema
     * @param $useful
     * @param $unuseful
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function SetNewAcademy($title, $body, $category, $slug, $status, $thumbnail, $language, $excerpt = null, $metatitle = null, $schema = null, $useful = null, $unuseful = null)
    {

        $response = sendResponse(0, "");

        $slug = str_replace(" ", "-", $slug);

        $sql = 'INSERT INTO tbl_academy (`admin_id`, `category_id`, `academy_slug`, `academy_title`, `academy_description`,
                         `academy_excerpt`, `academy_thumbnail`, `academy_schema`, `academy_meta_title`, `academy_language`,
                         `academy_useful`, `academy_unuseful`, `academy_status`, `academy_submit_time`, `academy_options`)
                VALUES (:admin_id,:category_id,:academy_slug,:academy_title,:academy_description,:academy_excerpt,
                        :academy_thumbnail,:academy_schema,:academy_meta_title,:academy_language,:academy_useful,:academy_unuseful,
                        :academy_status,:academy_submit_time,:academy_options)';

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $s = [];
            $s['update'] = [];

            $params = [
                'admin_id' => $admin_id,
                'category_id' => $category,
                'academy_slug' => $slug . "-" . substr(time(), -7),
                'academy_title' => $title,
                'academy_description' => $body,
                'academy_excerpt' => $excerpt,
                'academy_thumbnail' => $thumbnail,
                'academy_schema' => $schema,
                'academy_meta_title' => $metatitle,
                'academy_language' => $language,
                'academy_useful' => $useful,
                'academy_unuseful' => $unuseful,
                'academy_status' => $status,
                'academy_submit_time' => time(),
                'academy_options' => json_encode($s),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * Get Academy Info By ID
     *
     * @param      $id
     * @param null $status
     *
     * @return Object
     * @author  Tjavan
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getAcademyByID($id, $status = null)
    {
        $response = sendResponse(0, "");
        if (is_null($status)) {
            $sql = "SELECT * FROM `tbl_academy` WHERE academy_id=:academy_id ";
            $params = [
                "academy_id" => $id
            ];
        } else {
            $sql = "SELECT * FROM `tbl_academy` WHERE academy_status=:academy_status AND academy_id=:academy_id ";
            $params = [
                "academy_id" => $id,
                "academy_status" => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;


    }


    /**
     * Get Academy Info By ID
     *
     * @param      $id
     * @param null $status
     *
     * @return Object
     * @author  Tjavan
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getAcademyBySlug($slug, $status = null)
    {
        $response = sendResponse(0, "");
        if (is_null($status)) {
            $sql = "SELECT * FROM `tbl_academy` WHERE academy_slug=:academy_slug ";
            $params = [
                "academy_slug" => $slug
            ];
        } else {
            $sql = "SELECT * FROM `tbl_academy` WHERE academy_status=:academy_status AND academy_slug=:academy_slug ";
            $params = [
                "academy_slug" => $slug,
                "academy_status" => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;


    }


    /**
     * edit Academy
     *
     * @param $id
     * @param $title
     * @param $myContent
     * @param $category
     * @param $slug
     * @param $status
     * @param $image
     * @param $language
     * @param $excerpt
     * @param $metatitle
     * @param $schema
     * @param $useful
     * @param $unuseful
     *
     * @return stdClass
     * @author Tjavan
     */
    public static function editAcademy($id, $title, $myContent, $category, $slug, $status, $image, $language, $excerpt = null, $metatitle = null, $schema = null, $useful = null, $unuseful = null)
    {
        $response = sendResponse(0, "");

        $slug = str_replace(" ", "-", $slug);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $res = self::getAcademyByID($id);
            $temp = [];
            $slugOld = "";
            $imageOld = "";
            if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                $temp = $res->response[0]->academy_options;
                $slugOld = $res->response[0]->academy_slug;
                $imageOld = $res->response[0]->academy_thumbnail;
            }

            if (!empty($image)) {
                $imageOld = $image;
            }

            $e = explode("-", $slugOld);
            $slugOldConstant = end($e);


            if (!empty($temp)) {
                $value = json_decode($temp, true);

                $array = [];
                $array['create'] = $admin_id;
                $array['date'] = time();
                array_push($value['update'], ($array));

            }


            if (is_null($useful) || is_null($unuseful)) {
                $sql = 'UPDATE `tbl_academy` SET `category_id`=:category_id,`academy_slug`=:academy_slug
                     ,`academy_title`=:academy_title,`academy_description`=:academy_description,`academy_excerpt`=:academy_excerpt
                     ,`academy_thumbnail`=:academy_thumbnail,`academy_schema`=:academy_schema,`academy_meta_title`=:academy_meta_title
                     ,`academy_language`=:academy_language,`academy_status`=:academy_status,`academy_options`=:academy_options
                     WHERE `academy_id`=:academy_id';
                $params = [
                    'category_id' => $category,
                    'academy_slug' => $slug . "-" . $slugOldConstant,
                    'academy_title' => $title,
                    'academy_description' => $myContent,
                    'academy_excerpt' => $excerpt,
                    'academy_thumbnail' => $imageOld,
                    'academy_schema' => $schema,
                    'academy_meta_title' => $metatitle,
                    'academy_language' => $language,
                    'academy_status' => $status,
                    'academy_options' => json_encode($value),
                    'academy_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_academy` SET `category_id`=:category_id,`academy_slug`=:academy_slug
                     ,`academy_title`=:academy_title,`academy_description`=:academy_description,`academy_excerpt`=:academy_excerpt
                     ,`academy_thumbnail`=:academy_thumbnail,`academy_schema`=:academy_schema,`academy_meta_title`=:academy_meta_title
                     ,`academy_language`=:academy_language,`academy_useful`=:academy_useful,`academy_unuseful`=:academy_unuseful
                     ,`academy_status`=:academy_status,`academy_options`=:academy_options
                     WHERE `academy_id`=:academy_id';
                $params = [
                    'category_id' => $category,
                    'academy_slug' => $slug . "-" . $slugOldConstant,
                    'academy_title' => $title,
                    'academy_description' => $myContent,
                    'academy_excerpt' => $excerpt,
                    'academy_thumbnail' => $imageOld,
                    'academy_schema' => $schema,
                    'academy_meta_title' => $metatitle,
                    'academy_language' => $language,
                    'academy_useful' => $useful,
                    'academy_unuseful' => $unuseful,
                    'academy_status' => $status,
                    'academy_options' => json_encode($value),
                    'academy_id' => $id,
                ];
            }


            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Delete Academy
     *
     * @return Object
     * @author  Tjavan
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function deleteAcademy($id)
    {
        $response = sendResponse(0, "");

        $sql = "DELETE FROM `tbl_academy` WHERE `academy_id`=:academy_id;";
        $params = [
            'academy_id' => $id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Deleted Msg");
        }

        return $response;
    }

    public static function getNewsetsAcademy($status = "published")
    {
        $response = sendResponse(0, "");
        $lang = 'fa_IR';
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $lang = $_COOKIE['language'];
        }
        $sql = "SELECT * FROM `tbl_academy`
                inner join tbl_academy_categories tac on tac.category_id = tbl_academy.category_id
                where academy_language = :lang and academy_status=:status
                order by tbl_academy.academy_submit_time desc limit  10";
        $params = [
            "lang" => $lang,
            "status" => $status
        ];


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;

    }

    public static function getCategoryList($parent = null, $status = 'active')
    {
        $lang = 'fa_IR';
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $lang = $_COOKIE['language'];
        }
        $response = sendResponse(0, "");
        if (is_null($parent)) {
            $sql = "SELECT * FROM tbl_academy_categories tac where category_status = :status  and category_language =:lang and parent_id is null";
            $params = [
                "status" => $status,
                "lang" => $lang
            ];
        } else {
            $sql = "SELECT * FROM tbl_academy_categories tac where parent_id = :parent_id  and category_status = :status and category_language =:lang ";
            $params = [
                "parent_id" => $parent,
                "status" => $status,
                "lang" => $lang
            ];
        }


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    public static function getAcademyByCategory($category, $status = 'published', $limit = 500)
    {
        $lang = 'fa_IR';
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $lang = $_COOKIE['language'];
        }

        $sql = "SELECT * FROM tbl_academy 
                inner join tbl_academy_categories on tbl_academy.category_id = tbl_academy_categories.category_id
                where tbl_academy_categories.category_id = :category_id and academy_status = :status and academy_language =:lang
                order by tbl_academy.academy_submit_time desc
                limit $limit;";
        $params = [
            "category_id" => $category,
            "status" => $status,
            "lang" => $lang
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $output = [];
            foreach ($result->response as $index => $academy) {
                $item = new stdClass();
                $item->academy_thumbnail = Utils::fileExist($academy->academy_thumbnail, BOX_EMPTY);
                $item->category_name = $academy->category_name;
                $item->academy_id = $academy->academy_id;
                $item->academy_slug = $academy->academy_slug;
                $item->academy_title = $academy->academy_title;
                $item->academy_submit_time = $academy->academy_submit_time;
                $output[] = $item;
            }
            return sendResponse(200, "", $output);
        }
        return sendResponse(0, "");
    }
    public static function getAcademyByCategoryForAllTabs($category, $status = 'published', $limit = 500)
    {
        $lang = 'fa_IR';
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $lang = $_COOKIE['language'];
        }

        $sql = "SELECT * FROM tbl_academy 
                inner join tbl_academy_categories on tbl_academy.category_id = tbl_academy_categories.category_id
                where (tbl_academy_categories.category_id = :category_id or tbl_academy_categories.parent_id=:category_id) and academy_status = :status and academy_language =:lang
                order by tbl_academy.academy_submit_time desc
                limit $limit;";
        $params = [
            "category_id" => $category,
            "status" => $status,
            "lang" => $lang
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $output = [];
            foreach ($result->response as $index => $academy) {
                $item = new stdClass();
                $item->academy_thumbnail = Utils::fileExist($academy->academy_thumbnail, BOX_EMPTY);
                $item->category_name = $academy->category_name;
                $item->academy_id = $academy->academy_id;
                $item->academy_slug = $academy->academy_slug;
                $item->academy_title = $academy->academy_title;
                $item->academy_submit_time = $academy->academy_submit_time;
                $output[] = $item;
            }
            return sendResponse(200, "", $output);
        }
        return sendResponse(0, "");
    }

    public static function searchInAcademy($search_value, $category = null, $status = 'published')
    {
        $lang = 'fa_IR';
        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
            $lang = $_COOKIE['language'];
        }
        if (is_null($category) || empty($category)) {
            $sql = "SELECT * FROM tbl_academy 
                inner join tbl_academy_categories on tbl_academy.category_id = tbl_academy_categories.category_id
                where  academy_status = :status and academy_language =:lang
                and academy_title like  '%$search_value%' order by tbl_academy.academy_submit_time desc";
            $params = [
                'status' => $status,
                'lang' => $lang
            ];
        } else {
            $sql = "SELECT * FROM tbl_academy 
                inner join tbl_academy_categories on tbl_academy.category_id = tbl_academy_categories.category_id
                where tbl_academy_categories.category_id = :category_id and academy_status = :status and academy_language =:lang
                and academy_title like  '%$search_value%' order by tbl_academy.academy_submit_time desc";
            $params = [
                'category_id' => $category,
                'status' => $status,
                'lang' => $lang
            ];
        }
            $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $output = [];
            foreach ($result->response as $index => $academy) {
                $item = new stdClass();
                $item->academy_thumbnail = Utils::fileExist($academy->academy_thumbnail, BOX_EMPTY);
                $item->category_name = $academy->category_name;
                $item->academy_id = $academy->academy_id;
                $item->academy_slug = $academy->academy_slug;
                $item->academy_title = $academy->academy_title;
                $item->academy_submit_time = Utils::getTimeByLang($academy->academy_submit_time);
                $output[] = $item;
            }
            return sendResponse(200, "", $output);
        } elseif ($result->status == 204) {
            return sendResponse(204, "");
        }
        return sendResponse(0, "");
    }

    public static function updateAcademySubmitDate($id)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $res = self::getAcademyByID($id);
            $temp = [];
            if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                $temp = $res->response[0]->academy_options;
            }

            if (!empty($temp)) {
                $value = json_decode($temp, true);
                $array = [];
                $array['create'] = $admin_id;
                $array['date'] = time();
                array_push($value['update'], ($array));

            }

            $sql = 'UPDATE `tbl_academy` SET `academy_submit_time`=:academy_submit_time,`academy_options`=:academy_options
                     WHERE `academy_id`=:academy_id';
            $params = [
                'academy_submit_time' => time(),
                'academy_options' => json_encode($value),
                'academy_id' => $id,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }
}