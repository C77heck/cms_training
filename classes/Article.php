<?php

/**
 * Article
 *
 * a piece of writing for publication
 *  */
class Article
{
    /**
     * Unique identifier
     * @var integer
     */
    /**
     * the article title
     * @var string
     */
    /**
     * the article content
     * @var string
     */
    /**
     * the article publishing date
     * @var string
     */
    public $id;
    public $title;
    public $content;
    public $published_at;
    public $image_file;
    public $errors = [];
    /**
     * get all the articles
     *
     * @param object $conn Connection to the database
     * @return array An associative array of all the article records
     *
     * a piece of writing for publication
     *  */
    public static function getAll($conn)
    {
        $sql = "SELECT *
    FROM article
    ORDER BY published_at;";

        $results = $conn->query($sql);
        //we make the query like so

        return $results->fetchAll(PDO::FETCH_ASSOC);
        // fetch the articles with above method. we grab the associated id like so
    }

    /**
     * Get the article record based on the ID
     *
     * @param object $conn Connection to the database
     * @param integer $id the article ID
     * @param string $columns Optional list of columns for the select, defaults to
     *
     * @return mixed Amixed An object of this  class, or null if not found
     */
    public static function getById($conn, $id, $columns = '*')
    {
        $sql = "SELECT $columns
           FROM article
           WHERE id = :id";
        /* query string. notice how we predefine the value unless we call the function with an argument
        notice how we create a named parameter to be a placeholder of the value.
        better this way then adding question marks for readability */

        $stmt = $conn->prepare($sql);
        //we prep the statement

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        /* Notice how we bind the values. we point to the place holder then we assign the value to
it and we declare its value to be an integer. */

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Article');
        /* we need this so we can return an object rather than an array */
        if ($stmt->execute()) {
            //execute returns true?

            return $stmt->fetch();
            //  then we return the results
        }
    }

    /**
     * update articles
     *
     * @param object $conn Connection to the database
     *
     * @return boolean True if the update was succesfull and false if not.
     *
     */
    public function update($conn)
    {
        if ($this->validate()) {
            /* we validate inside the objects function. we could move the code outside. but it is better
practice this way. */
            $sql = "UPDATE article
                SET title = :title,
                content = :content,
                published_at = :published_at
                WHERE id = :id";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

            /* We bind the values like so first is the placeholder param then the actual value then the datatype*/

            if ($stmt->published_at == '') {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
            }
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Set the article categories
     *
     * @param object $conn Connection to the database
     * @param array $ids Category IDs
     *
     * @return void
     */
    public function setCategories($conn, $ids)
    {
        if ($ids) {
            $sql = "INSERT IGNORE INTO article_category (article_id, category_id)
                    VALUES ";
            /* we set the categories like so. it doesn't make too much sense to be honest...
the IGNORE is there to ignore ALL errors. so be careful in its usage.*/

            $values = [];

            foreach ($ids as $id) {
                $values[] = "({$this->id}, ?)";
            }
            /* this essentially creates the usual multi row adding like if done this
VALUES value1,value2, and so on. but we need the comas and such so see below method */
            $sql .= implode(", ", $values);

            $stmt = $conn->prepare($sql);

            foreach ($ids as $i => $id) {
                /* we loop through as we can have multiple ids in it. and then
                we bind the values individually
                notice how we grab the index and then use it below. and keep on adding one on top*/
                $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
            }
            $stmt->execute();
            //then we execute the statement
        }
        /* DELETING LOGIC WHEN WE CHANGE THE CATEGORIES(UPDATING...) */
        $sql = "DELETE FROM article_category
        WHERE article_id = {$this->id}";

        if ($ids) {

            $placeholders = array_fill(0, count($ids), '?');

            $sql .= " AND category_id NOT IN (" . implode(", ", $placeholders) . ")";
            /* we are updating the sql statement like above if we get multiple ids */
        }

        $stmt = $conn->prepare($sql);

        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        }

        $stmt->execute();
    }

    /**
     * Validate the article properties
     *
     * @return boolean true if properties are valid and false otherwise.
     */
    protected function validate()
    {

        if ($this->title == '') {
            $this->errors[] = 'Title is required';
        }
        //we check if value has been added or not. if not we create an arror

        if ($this->content == '') {
            $this->errors[] = 'Content is required';
        }

        if ($this->published_at != '') {
            $this->date_time = date_create_from_format('Y-m-d H:i:s', $this->published_at);
            //we validate the date and time input

            if ($this->date_time === false) {
                // its another check if the date is the right format or not.

                $this->errors[] = 'Invalid date and time';
            } else {

                $this->date_errors = date_get_last_errors();

                if ($this->date_errors['warning_count'] > 0) {
                    // this is to avoid invalid inserts like 30th febr and so on.

                    $this->errors[] = 'Invalid date and time';
                }
            }
        }

        return empty($this->errors);
        // in order to return a boolean we check if it's empty. smart
    }

    /**
     * Delete an article
     *@param object $conn Connection to database
     * @return boolean true if delete was successful and false otherwise.
     */

    public function delete($conn)
    {
        $sql = "DELETE FROM article
    WHERE id = :id";
        //sql query to delete
        $stmt = $conn->prepare($sql);
        //prep the statement
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        //we bind the values
        return $stmt->execute();
        //then we execute the query
    }

    /**
     * Create an article
     *@param object $conn Connection to database
     * @return boolean true if the insert was successful and false otherwise.
     */

    public function create($conn)
    {
        if ($this->validate()) {
            $sql = "INSERT INTO article (title, content, published_at)
        VALUES (:title, :content, :published_at)";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

            /* We bind the values like so first is the placeholder param then the actual value then the datatype*/

            if ($this->published_at == '') {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
            }
            if ($stmt->execute()) {
                $this->id = $conn->lastInsertId();
                //we grab the inserted item id in order to allow us to redirect to the new article
                return true;
            }
        } else {
            return false;
        }
    }
    /**
     * Get a page of articles
     *
     * @param object $conn Connection to the database
     * @param integer $limit Number of records to return
     * @param integer $offset Number of records to skip
     *
     * @return array An associative array of the page of article records
     */
    public static function getPage($conn, $limit, $offset, $only_published = false)
    {
        /* We use this variable to add extra query to the subquery */
        $condition = $only_published ? ' WHERE  published_at IS NOT NULL' : '';
        $sql = "SELECT a.*, category.name AS category_name
        FROM (SELECT *
                FROM article
                $condition
                ORDER BY published_at
                LIMIT :limit
                OFFSET :offset) AS a
                LEFT JOIN article_category
                ON a.id = article_category.article_id
                LEFT JOIN category
                ON article_category.category_id = category.id";
        /*
we create a sub-query and use the AS keyword to create an alias which then we can work on in the
on the main query. where we join the article category if a.id is equal to any article_category
id.

we select the collection we want the data from. we set the order by date
then we have a limit placeholder added which will define how many it grabs per fetch
and offset where it should start from. so we ommit the first 3 for instance. */
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        /* below we deal with the propblem of items appearing more than once. we want one article to appear
once and then create an array of categories for the display.  */
        $articles = [];
        $previous_id = null;
        foreach ($results as $row) {
            /* we loop through the results and check if the ids match or not. */
            $article_id = $row['id'];

            if ($article_id != $previous_id) {
                /* if id is the same then we won't add it. */
                $row['category_names'] = []; //in case its a new article.
                $article[$article_id] = $row;
                /* we assign the row(article) and this will be returned */
            }
            $articles[$article_id]['category_names'][] = $row['category_name'];

            $previous_id = $article_id;
            //we assign this id to the prev one so we ommit if it turns up again
        }
        return $article;
    }

    /**
     * Get a count of the total number of records
     *
     * @param object $conn Connection to the database
     *
     * @return integer The total number of records
     */
    public static function getTotal($conn, $only_published = false)
    {
        $condition = $only_published ? ' WHERE  published_at IS NOT NULL' : '';

        return $conn->query("SELECT COUNT(*) FROM article{$condition}")->fetchColumn();
        /* this is how we grab how many items we have in the queried database in total */
    }
    /**
     * Update the image file property
     *
     * @param object $conn Connection to the database
     * @param string $filename The filename of the image file
     *
     * @return boolean True if it was successful, false otherwise
     */
    public function setImageFile($conn, $filename)
    {
        $sql = "UPDATE article
                SET image_file = :image_file
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':image_file', $filename, $filename === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        /* IF the file name value is null then we nwant to delete hence the data type assigment. */
        return $stmt->execute();
    }
    /**
     * Update the image file property
     *
     * @param object $conn Connection to the database
     * @param string $filename The filename of the image file
     *
     * @return boolean True if it was successful, false otherwise
     */
    public static function getWithCategories($conn, $id, $only_published = false)
    {
        $sql = "SELECT article.*, category.name AS category_name
        FROM article
        LEFT JOIN article_category
        ON article.id = article_category.article_id
        LEFT JOIN category
        ON article_category.category_id = category.id
        WHERE article.id = :id";


        if ($only_published) {
            /* if the published is true we edit the sql query string */
            $sql .= ' AND article.published_at IS NOT NULL';
            //We append this to the query to grab data that its published_at !== null
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        /* as we might get more records we set it to associative array.
    look into it down the line. */
    }

    /**
     * Get the article's categories
     *
     * @param object $conn Connection to the database
     *
     * @return array The category data
     */
    public function getCategories($conn)
    {
        $sql = "SELECT category.*
                FROM category
                JOIN article_category
                ON category.id = article_category.category_id
                WHERE article_id = :id";
        /* where clause is to restrict to get this table with this id */
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Publish the article, setting the published_at field to the current date and time
     *
     * @param object $conn Connection to the database
     *
     * @return mixed The published at date and time if successful, null otherwise
     */
    public function publish($conn)
    {
        $sql = "UPDATE article
                SET published_at = :published_at
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $published_at = date("Y-m-d H:i:s");
        $stmt->bindValue(':published_at', $published_at, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $published_at;
        }
    }
}
