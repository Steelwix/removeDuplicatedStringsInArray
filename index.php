 public function removeDuplicatedTagsInString(): array
    {

        //Upper or lower case?
        // Do I keep high-tech, hightech or high tech?
        //Ban "Produit"

        //    public function newTag($tag, $supplier_id,)
        //    {
        //        $newTag = $this->dataBase->prepare("INSERT INTO Tags(id, value) VALUES (NULL, :tag)");
        //        $newTag->execute(array(':tag' => $tag));
        //          $tag_id = $this->dataBase->lastInsertId();
        //         $linkTag = $this->dataBase->prepare("INSERT INTO SupplierTag(id, supplier_id, tag_id, weight) VALUES (NULL, :supplier_id, :tag_id, NULL)");
        //        $linkTag->execute(array(':supplier_id' => $supplier_id, ':tag_id' => $tag_id ));
        //    }
        set_time_limit(0); // 0 = no limits
        $file = fopen('../tags.csv', 'r');

    $tags = array();
    $database = array();
    $i = 0;

//        while (($results = fgetcsv($file)) !== false)
//        {
//        $database[$i]['id'] = $results[0];
//            $database[$i]['tag'] = $results[1];
//            $database[$i]['supplier_id']=$results[2];
//            //$tags[$i] = $results[1];
//        $i++;
//            }
        //fclose($file);
        // FAKE CSV FILE
//            $database[0]['id'] = 1;
//           $database[0]['tag'] = "champagnes";
//            $database[0]['supplier_id']= 1;
//            $tags[0] = $database[0]['tag'];
//
//        $database[1]['id'] = 2;
//        $database[1]['tag'] = "champagne";
//        $database[1]['supplier_id']= 2;
//        $tags[1] = $database[1]['tag'];

        $database[2]['id'] = 3;
        $database[2]['tag'] = "bonbon / chocolats";
        $database[2]['supplier_id']= 666;
        $tags[2] = $database[2]['tag'];

//        $database[3]['id'] = 2;
//        $database[3]['tag'] = "champagne";
//        $database[3]['supplier_id']= 3;
//        $tags[3] = $database[3]['tag'];

        $database[4]['id'] = 5;
        $database[4]['tag'] = "bonbons";
        $database[4]['supplier_id']= 10;
        $tags[4] = $database[4]['tag'];

        $database[5]['id'] = 5;
        $database[5]['tag'] = "bonbons";
        $database[5]['supplier_id']= 20;
        $tags[5] = $database[5]['tag'];

        $database[6]['id'] = 7;
        $database[6]['tag'] = "chocolat";
        $database[6]['supplier_id']= 100;
        $tags[6] = $database[6]['tag'];

        $database[7]['id'] = 7;
        $database[7]['tag'] = "chocolat";
        $database[7]['supplier_id']= 200;
        $tags[7] = $database[7]['tag'];
    //FAKE CSV FILE
        echo '<pre>', 'tags  : ',var_dump($tags), '</pre>';
        echo '<pre>', 'current database : ',var_dump($database), '</pre>';
        $tags_count = array_count_values($tags); //Classer les tags par fréquence
        asort($tags_count);
        $reversed_tags_count = array_reverse($tags_count);
        $clearTags = false;

        while ($clearTags === false) {
            end($reversed_tags_count); //Identification du dernier tag de l'array
            $lastElement = key($reversed_tags_count);
            foreach ($reversed_tags_count as $tag => $count) {
                echo '<pre style="background-color: darkseagreen">', 'WORKING ON  : ',var_dump($tag), '</pre>';
                //echo '<pre style="background-color: darkseagreen">', 'TAGS STATUS  : ',var_dump($tags), '</pre>';
                //Foreach qui va vérifier les tags dans l'odre de fréquence

                if (preg_match('/[\&\,\/]/', $tag, $matches)) { //Les symboles / & et , sont remplacés par "et"
                    //echo '<pre style="background-color:orange">', '&/, Symbol detected in : ',var_dump($tag), '</pre>';
                    list($tags, $database) = $this->dismantleSlashedTags($tags, $tag, $matches, $database);
                    list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database );
                    break;

                }
                if(preg_match('/[A-Z]/', $tag)){
                    //echo '<pre style="background-color:orange">', 'Caps detected in : ',var_dump($tag), '</pre>';
                    $lowTag = $this->removeCaps($tag); //Retire les uppercase

                    list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database , $tag, $lowTag);
                    break;

                }

                if (preg_match('/\'/', $tag)) { // L'apostrophe est remplacé par un espace
                    //echo '<pre style="background-color:orange">', 'Apostrophe detected in : ',var_dump($tag), '</pre>';
                    $clearTag = $this->dismantleApostrophe($tag);
                    list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database , $tag, $clearTag);
                    break;

                }

                $subTag = substr($tag, -1); //Detection de la dernière lettre du tag
                if($subTag === " "){ //Retire l'espace à la fin du tag si il y en a un
                    //echo '<pre style="background-color:orange">', 'Space detected in : ',var_dump($tag), '</pre>';
                    $clearTag = substr($tag, 0, -1);
                    //echo '<pre style="background-color:#FFCC00">', 'Space removed : ',var_dump($clearTag), '</pre>';
                    list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database , $tag, $clearTag);
                    break;
                }
                $clearTag = $this->supprimerAccents($tag, true, null, ['(', ')', ' ', '-']); //Retire les symboles
                if($tag != $clearTag){
                    list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database , $tag, $clearTag);
                    break;
                }
                $update = $this->pluralChecker($tags, $database, $tag, $subTag);
                if(is_array($update)){
                    //echo '<pre style="background-color:orange">', 'plural constraint detected in : ',var_dump($tag), '</pre>';
                    list($tags, $reversed_tags_count, $database) = $this->pluralChecker($tags,$database, $tag, $subTag);
                    break; //Relancement du foreach avec le nouvel array
                }
                //echo '<pre style="background-color:orange">', var_dump($tags), '</pre>';
            foreach ($tags as $checkedTag){
                if($tag == $checkedTag){
                    $tagKey = array_search($tag, $tags);
                    $keys = array_keys($tags, $tag);
                    foreach ($keys as $key=>$value){
                        if($tagKey != $value){
                            echo '<pre style="background-color:yellow">', 'DETECTED DOUBLE TAGS ON ',var_dump($tags[$tagKey]),var_dump($tagKey),var_dump($tags[$value]),var_dump($value), '</pre>';
                            if($database[$tagKey]['id'] != $database[$value]['id'] && $database[$value]['id'] != null){
                                echo '<pre style="background-color:orange">', '$database[$tagKey][id] = ',var_dump($database[$tagKey]['id']), '</pre>';
                                echo '<pre style="background-color:orange">', '$database[$value][id] = ',var_dump($database[$value]['id']), '</pre>';
                                echo '<pre style="background-color:orange">', 'Reassinging  ',var_dump($tag), '</pre>';
                                list($tags, $database) =$this->reassignFormatedTag($database, $tags, $tagKey, $value);


                                list($tags, $reversed_tags_count, $database) = $this->updateTagsArray($tags, $database , $tag, $clearTag);
                                break;
                            }
                        }
                    }
                }
            }

                if ($tag == $lastElement) { //Si le tag analysé est le dernier du tableau, alors on arrête le script
                    echo '<pre style="background-color:#00FF00">', 'tags are ready to go : ',var_dump($tags), '</pre>';
                    echo '<pre style="background-color:#00FF00">', 'new database : ',var_dump($database), '</pre>';
                    $clearTags = true;
                }
            }
        }
//        $file = fopen('../output.csv', 'w');
//    $export = array();
//        foreach ($tags as $tag){
//            $export[] = array($tag);
//        }
//        foreach ($export as $fields){
//            fputcsv($file, $fields);
//        }
//
//        fclose($file);
        return $tags;
    }
    public function reassignFormatedTag(array $database, array $array, int $tagKey, ?int $trueKey=null){
    $formatedTag = array();
        $formatedTag['id'] = $database[$trueKey]['id'];
        $formatedTag['tag'] = $database[$trueKey]['tag'];
    $formatedTag['supplier_id'] = $database[$tagKey]['supplier_id'];
    $newTag = array('id' => $formatedTag['id'], 'tag' => $formatedTag['tag'], 'supplier_id' => $formatedTag['supplier_id']);
    unset($array[$tagKey]);
    unset($database[$tagKey]);
    $array[] = $formatedTag['tag'];
    $database[] =   $newTag;
        echo '<pre style="background-color:#0aa2c0">','DATABASE REASSIGNED WITH  : ',var_dump($newTag), var_dump($database), '</pre>';
        $result[0] = $array; //La nouvelle valeur de $tags est stockée dans $result[0]
        $result[1] = $database;
        return $result;




    }
    public function pluralChecker(array $array, array $database, string $string, string $subString){
        if ($subString === "s" || $subString === "x") { //Si la dernière lettre peut symboliser le pluriel
            $singular = substr($string, 0, -1);    // On définit une variable $singular qui représente notre tag au singulier


            if (in_array($singular, $array)) { //Si le tag au singulier existe dans l'array de la bdd
                $update = $this->updateTagsArray($array, $database , $singular, $string);

            }
        } else { //Si la derniere lettre n'est pas synonyme de pluriel
            $plural = $string  . "s"; // On définit une variable $plural qui représente notre tag au pluriel
            if (in_array($plural, $array)) { //Si le tag au pluriel existe dans l'array de la bdd
                $update = $this->updateTagsArray($array, $database , $plural, $string);
            }
        }
        if(isset($update)){
    return $update;
        }

    }
        public function dismantleSlashedTags(array $array, string $string, array $matches, array $database): array
        {

            $tagKey = array_search($string, $array);
            $supplier = $database[$tagKey]['supplier_id'];
            foreach ($matches as $match){ //Peu importe le symbole detecté, on le remplace par "et"
                $splitedTags = explode($match, $string);
                foreach($splitedTags as $tag){
                        $formatedTag = array();
                        $formatedTag['id'] = null;
                        $formatedTag['tag'] = $tag;
                        $formatedTag['supplier_id'] = $supplier;
                        $newTag = array('id' => $formatedTag['id'], 'tag' => $formatedTag['tag'], 'supplier_id' => $formatedTag['supplier_id']);
                        unset($array[$tagKey]);
                        unset($database[$tagKey]);
                        $array[] = $formatedTag['tag'];
                        $database[] =   $newTag;
                    echo '<pre style="background-color:#0aa2c0">','DATABASE REASSIGNED WITH  : ',var_dump($newTag), var_dump($database), '</pre>';
                        $result[0] = $array; //La nouvelle valeur de $tags est stockée dans $result[0]
                        $result[1] = $database;

                }
                }
            return $result;
        }
        public function dismantleApostrophe($string): string
        {
            $clearString = str_replace('\'', ' ', $string);// Remplacement des apostrophes par un espace
            return $clearString;
        }
        public function supprimerAccents(string $string, bool $remove_special_char = true, ?string $replace_space = null, array $exceptions = []): string
        {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII;', $string);
            $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string); //Adaptation du format texte pour supprimer
            //les accents

            if ($remove_special_char) $string = $this->supprimerSpecialChar($string, $exceptions); //Suppression des symboles
            $string = preg_replace('/\s+/', ' ', $string); //Refactorisation des espaces de trop

            $string = trim($string);
            if ($replace_space !== null) $string = str_replace(' ', $replace_space, $string);

            return $string;
        }
        public function supprimerSpecialChar(string $string, array $exceptions = []): string //Suppression caractères spéciaux
        {
            $regex = '/[^\p{L}';

            foreach ($exceptions as $exception) {
                $regex .= '\\' . $exception;
            }

            $regex .= ']/u';

            return preg_replace($regex, '', $string);
        }
        public function removeCaps($string): string //Transformation des majuscules en minuscules
        {
            $string = strtolower($string);
            return $string;
        }
    public function updateTagsArray(array $array, array $database, ?string $oldValue = null, ?string $newValue = null): array{
        if($oldValue != null && $newValue != null){
            $keys = array_keys($array, $oldValue); //Detection du tag a modifier dans le tableau des tags
            foreach ($keys as $key => $value) {
                $array[$value] = $newValue; //Assignation de la nouvelle valeur
                $database[$value]['tag'] = $newValue;
            }
        }
        $array_count = array_count_values($array); //Rafraichissement des arrays
        asort($array_count);
        $reversed_array_count = array_reverse($array_count);
        $result[0] = $array; //La nouvelle valeur de $tags est stockée dans $result[0]
        $result[1] = $reversed_array_count;//La nouvelle valeur de reverses_tag_count est stockée dans $result[1]
        $result[2] = $database;


        echo '<pre style="background-color:#4dd4ac">', 'tag updates ', '</pre>';


        return $result;

    }
    public function getSupplier(array $database, int $tagKey): int{
        $supplier = $database[$tagKey]['supplier_id'];
        return $supplier;
    }
