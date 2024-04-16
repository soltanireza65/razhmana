<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Post
{

    /**
     * Get All Post Categories By Status
     * @param $status
     * @return stdClass
     * @author Tjavan
     */
    public static function getAllPostCategories($status = null)
    {
        $response = sendResponse(0, "");

        if (is_null($status)) {
            $sql = "SELECT category_id,category_name,category_language,category_status,category_priority FROM `tbl_post_categories` ORDER BY category_id DESC ;";
            $result = DB::rawQuery($sql, []);
        } else {
            $sql = "SELECT category_id,category_name,category_language,category_status,category_priority FROM `tbl_post_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
            $result = DB::rawQuery($sql, ['category_status' => $status]);
        }
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * get Count Post By Category ID
     * @param $categoryId
     * @return stdClass
     * @author Tjavan
     */
    public static function getCountPostByCategory($categoryId)
    {
        $response = sendResponse(0, "");


        $sql = "SELECT count(*) AS count FROM `tbl_posts` WHERE `category_id`=:category_id";
        $result = DB::rawQuery($sql, ['category_id' => $categoryId]);


        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Insert New Post Category
     * @param $title
     * @param $language
     * @param $status
     * @param $priority
     * @param $metaTitle
     * @param $metaDesc
     * @param $schema
     * @return stdClass
     * @author Tjavan
     */
    public static function SetNewPostCategory($title, $language, $status, $priority, $metaTitle, $metaDesc, $schema)
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

            $sql = 'INSERT INTO tbl_post_categories (`category_name`, `category_language`, `category_meta_title`, `category_meta_desc`,
                    `category_schema`, `category_status`,`category_priority`, `category_options`) 
                    VALUES (:category_name,:category_language,:category_meta_title,:category_meta_desc,
                    :category_schema,:category_status,:category_priority,:category_options);';
            $params = [
                'category_name' => $title,
                'category_meta_title' => $metaTitle,
                'category_meta_desc' => $metaDesc,
                'category_schema' => $schema,
                'category_status' => $status,
                'category_priority' => $priority,
                'category_language' => $language,
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
     * Get Post Category Info By ID
     * use category-post-edit.php
     * @param $id int
     * @return Object
     * @author Tjavan
     */
    public static function getPostCategoryById($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_post_categories` WHERE category_id=:category_id";
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
     * Update Post Category
     * @param $category_id
     * @param $title
     * @param $language
     * @param $status
     * @param $priority
     * @param $metaTitle
     * @param $metaDesc
     * @param $schema
     * @return stdClass
     * @author Tjavan
     */
    public static function editPostCategory($category_id, $title, $language, $status, $priority, $metaTitle = null, $metaDesc = null, $schema = null)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getPostCategoryById($category_id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->category_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));
        }


        $sql = "UPDATE `tbl_post_categories` SET `category_name`=:category_name,`category_language`=:category_language,
                `category_meta_title`=:category_meta_title,`category_meta_desc`=:category_meta_desc,`category_schema`=:category_schema,
                `category_status`=:category_status,`category_priority`=:category_priority,`category_options`=:category_options WHERE category_id=:category_id";
        $params = [
            'category_name' => $title,
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
     * delete Category Post
     * @param $categoryID
     * @param $replaceID
     * @return stdClass
     * @author Tjavan
     */
    public static function deleteCategoryPost($categoryID, $replaceID)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_posts` SET `category_id`=:replaceID WHERE category_id=:categoryID;
        DELETE FROM `tbl_post_categories` WHERE `category_id`=:category_id";
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
     * Get Analysis Info By ID
     * @param $id
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getPostByID($id, $status = null)
    {
        $response = sendResponse(0, "");
        if (is_null($status)) {
            $sql = "SELECT * FROM `tbl_posts` WHERE post_id=:post_id ";
            $params = [
                "post_id" => $id
            ];
        } else {
            $sql = "SELECT * FROM `tbl_posts` WHERE post_status=:post_status AND post_id=:post_id ";
            $params = [
                "post_id" => $id,
                "post_status" => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;


    }


    /**
     * Insert New Post
     * @param $title
     * @param $body
     * @param $category
     * @param $slug
     * @param $status
     * @param $thumbnail
     * @param $sidebar
     * @param $breadcrumb
     * @param string $excerpt
     * @param string $tags
     * @param string $schema
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function SetNewPost($title, $body, $category, $slug, $status, $thumbnail, $language, $excerpt = null, $metatitle = null, $schema = null, $useful = null, $unuseful = null)
    {

        $response = sendResponse(0, "");

        $slug = str_replace(" ", "-", $slug);

        $sql = 'INSERT INTO tbl_posts (`admin_id`, `category_id`, `post_slug`, `post_title`, `post_description`,
                         `post_excerpt`, `post_thumbnail`, `post_schema`, `post_meta_title`, `post_language`,
                         `post_useful`, `post_unuseful`, `post_status`, `post_submit_time`, `post_options`)
                VALUES (:admin_id,:category_id,:post_slug,:post_title,:post_description,:post_excerpt,
                        :post_thumbnail,:post_schema,:post_meta_title,:post_language,:post_useful,:post_unuseful,
                        :post_status,:post_submit_time,:post_options)';

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
                'post_slug' => $slug . "-" . substr(time(), -7),
                'post_title' => $title,
                'post_description' => $body,
                'post_excerpt' => $excerpt,
                'post_thumbnail' => $thumbnail,
                'post_schema' => $schema,
                'post_meta_title' => $metatitle,
                'post_language' => $language,
                'post_useful' => $useful,
                'post_unuseful' => $unuseful,
                'post_status' => $status,
                'post_submit_time' => time(),
                'post_options' => json_encode($s),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * Update Post By ID
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
     * @return stdClass
     * @author Tjavan
     */
    public static function editPost($id, $title, $myContent, $category, $slug, $status, $image, $language, $excerpt = null, $metatitle = null, $schema = null, $useful = null, $unuseful = null)
    {
        $response = sendResponse(0, "");

        $slug = str_replace(" ", "-", $slug);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $res = self::getPostByID($id);
            $temp = [];
            $slugOld = "";
            $imageOld = "";
            if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                $temp = $res->response[0]->post_options;
                $slugOld = $res->response[0]->post_slug;
                $imageOld = $res->response[0]->post_thumbnail;
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
                $sql = 'UPDATE `tbl_posts` SET `category_id`=:category_id,`post_slug`=:post_slug
                     ,`post_title`=:post_title,`post_description`=:post_description,`post_excerpt`=:post_excerpt
                     ,`post_thumbnail`=:post_thumbnail,`post_schema`=:post_schema,`post_meta_title`=:post_meta_title
                     ,`post_language`=:post_language,`post_status`=:post_status,`post_options`=:post_options
                     WHERE `post_id`=:post_id';
                $params = [
                    'category_id' => $category,
                    'post_slug' => $slug . "-" . $slugOldConstant,
                    'post_title' => $title,
                    'post_description' => $myContent,
                    'post_excerpt' => $excerpt,
                    'post_thumbnail' => $imageOld,
                    'post_schema' => $schema,
                    'post_meta_title' => $metatitle,
                    'post_language' => $language,
                    'post_status' => $status,
                    'post_options' => json_encode($value),
                    'post_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_posts` SET `category_id`=:category_id,`post_slug`=:post_slug
                     ,`post_title`=:post_title,`post_description`=:post_description,`post_excerpt`=:post_excerpt
                     ,`post_thumbnail`=:post_thumbnail,`post_schema`=:post_schema,`post_meta_title`=:post_meta_title
                     ,`post_language`=:post_language,`post_useful`=:post_useful,`post_unuseful`=:post_unuseful
                     ,`post_status`=:post_status,`post_options`=:post_options
                     WHERE `post_id`=:post_id';
                $params = [
                    'category_id' => $category,
                    'post_slug' => $slug . "-" . $slugOldConstant,
                    'post_title' => $title,
                    'post_description' => $myContent,
                    'post_excerpt' => $excerpt,
                    'post_thumbnail' => $imageOld,
                    'post_schema' => $schema,
                    'post_meta_title' => $metatitle,
                    'post_language' => $language,
                    'post_useful' => $useful,
                    'post_unuseful' => $unuseful,
                    'post_status' => $status,
                    'post_options' => json_encode($value),
                    'post_id' => $id,
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
     * update Post Submit Date
     * @param $id
     * @return stdClass
     */
    public static function updatePostSubmitDate($id)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $res = self::getPostByID($id);
            $temp = [];
            if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                $temp = $res->response[0]->post_options;
            }

            if (!empty($temp)) {
                $value = json_decode($temp, true);
                $array = [];
                $array['create'] = $admin_id;
                $array['date'] = time();
                array_push($value['update'], ($array));

            }

            $sql = 'UPDATE `tbl_posts` SET `post_submit_time`=:post_submit_time,`post_options`=:post_options
                     WHERE `post_id`=:post_id';
            $params = [
                'post_submit_time' => time(),
                'post_options' => json_encode($value),
                'post_id' => $id,
            ];

            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Delete Post
     * @param $id
     * @return stdClass
     * @author Tjavan
     */
    public static function deletePost($id)
    {
        $response = sendResponse(0, "");

        $sql = "DELETE FROM `tbl_posts` WHERE `post_id`=:post_id;";
        $params = [
            'post_id' => $id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Deleted Msg");
        }

        return $response;
    }


    /**
     * Get Post By Slug
     * @param $slug
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getPostBySlug($slug, $status = null)
    {
        $response = sendResponse(0, "");
        if (is_null($status)) {
            $sql = "SELECT * FROM `tbl_posts` WHERE post_slug=:post_slug ";
            $params = [
                "post_slug" => $slug
            ];
        } else {
            $sql = "SELECT * FROM `tbl_posts` WHERE post_status=:post_status AND post_slug=:post_slug ";
            $params = [
                "post_slug" => $slug,
                "post_status" => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;


    }


    /**
     * Get Post By Limit
     * @param $slug
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getPostByLimit($limitStart, $limit, $language = null)
    {
        $response = sendResponse(0, "");

        if (is_null($language)) {
            if (isset($_COOKIE['language'])) {
                $language = $_COOKIE['language'];
            } else {
                $language = 'fa_IR';
            }
        }

        $sql = "SELECT * FROM `tbl_posts` INNER JOIN `tbl_post_categories` ON tbl_posts.category_id = tbl_post_categories.category_id
                    WHERE post_status=:post_status AND tbl_post_categories.category_language=:category_language AND tbl_post_categories.category_status=:category_status ORDER BY post_submit_time DESC LIMIT {$limitStart},{$limit} ";
        $params = [
            "post_status" => 'published',
            "category_status" => 'active',
            "category_language" => $language,
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;


    }

}