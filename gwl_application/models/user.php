<?php 

class User extends CI_Model {

    var $errorMessage = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // hash password
    function hashPassword($password)
    {
        $salt = $this->config->item('password_salt');
        return hash("sha256", $password . $salt);
    }
    
    // register user
    function register($email, $username, $password)
    {
        // hash password
        $hashPassword = $this->hashPassword($password);

        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username));
        if ($query->num_rows() > 0)
        {
            $this->errorMessage = 'Sorry duder. This username is already taken. Bad luck!';
            return false;
        }

        // add user to db
        $data = array(
           'Username' => $username,
           'Password' => $hashPassword,
           'Email' => $email
        );

        // if added successfully 
        if($this->db->insert('users', $data)) {
            // login user
            if($this->login($username, $hashPassword, true)) {
                // success
                return true;
            } else {
                // error
                $this->errorMessage = 'User created but failed to login. Try logging in I guess?';
                return false;
            }
        } else {
            // error
            $this->errorMessage = 'Something went wrong! Please try again I guess?';
            return false;
        }
    }

    // login user
    function login($username, $password, $passwordIsHashed)
    {
        $hashPassword = $passwordIsHashed ? $password : $this->hashPassword($password);

        // check login is correct
        $query = $this->db->get_where('users', array('Username' => $username, 'Password' => $hashPassword));
        if ($query->num_rows() == 1)
        {
            // create session
            $row = $query->first_row();
            $newdata = array(
                'UserID' => $row->UserID,
                'Username' => $row->Username,
                'Admin' => $row->Admin,
                'DateTimeFormat' => $row->DateTimeFormat,
                'ProfileImage' => $row->ProfileImage == null ? "gwl_default.jpg" : $row->ProfileImage
            );
            $this->session->set_userdata($newdata);
            
            // success
            return true;
        } else {
            // error, incorrect login details
            return false;
        }
    }

    // logout user
    function logout()
    {
        $this->session->sess_destroy();
    }

    // update user profile
    function updateProfile($userID, $email, $username, $dateFormat, $bio)
    {
        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username, 'UserID !=' => $userID));
        if ($query->num_rows() > 0)
        {
            $this->errorMessage = 'Sorry duder. This username is already taken. Bad luck!';
            return false;
        }

        // add user to db
        $data = array(
           'Username' => $username,
           'Email' => $email,
           'DateTimeFormat' => $dateFormat,
           'Bio' => $bio
        );

        $this->db->where('UserID', $userID);
        $this->db->update('users', $data); 

        // update session
        $sessionData = array(
            'Username' => $username,
            'DateTimeFormat' => $dateFormat,
        );
        $this->session->set_userdata($sessionData);

        return true;
    }

    // get user by ID
    function getUserByID($userID)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // update user profile image
    function updateProfileImage($userID, $profileImage)
    {
        $newdata = array(
            'ProfileImage' => $profileImage
        );

        // update database
        $this->db->where('UserID', $userID); 
        $this->db->update('users', $newdata); 

        // update session
        $this->session->set_userdata($newdata);
    }

    // record new user event
    function addUserEvent($userID, $gameID, $listID, $statusID, $currentlyPlaying) 
    {
        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'DateStamp' => date("Y-m-d H:i:s")
        );

        if($listID != null) $data['ListID'] = $listID;
        if($statusID != null) $data['StatusID'] = $statusID;
        if($currentlyPlaying != null) $data['CurrentlyPlaying'] = ($currentlyPlaying === "true");

        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->where('UserID', $userID); 
        $this->db->where('GameID', $gameID); 
        $this->db->where('DateStamp >', date("Y-m-d H:i:s", strtotime('-24 hour'))); 
        $query = $this->db->get();

        // if event exists for User/Game in last 24 hours, update it rather than adding a new one
        if($query->num_rows() == 1)
        {
            $this->db->where('EventID', $query->first_row()->EventID); 
            return $this->db->update('userEvents', $data); 
        } else {
            return $this->db->insert('userEvents', $data); 
        }
    }

    // get list of events by UserID
    function getUserEvents($userID, $gbID, $DateTimeFormat, $offset, $resultsPerPage) 
    {
        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->join('games', 'userEvents.GameID = games.GameID');
        $this->db->join('users', 'userEvents.UserID = users.UserID');
        $this->db->join('lists', 'userEvents.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'userEvents.StatusID = gameStatuses.StatusID', 'left');
        if($userID != null) $this->db->where('userEvents.UserID', $userID); 
        if($gbID != null) $this->db->where('games.GBID', $gbID); 
        $this->db->order_by("DateStamp", "desc"); 
        $this->db->limit($resultsPerPage, $offset);
        $events = $this->db->get()->result();

        // loop through events
        $this->load->model('Game');
        $this->load->model('Time');
        $this->load->library('md');
        foreach ($events as $event)
        {
            // build array of events
            $event->eventItems = array();
            if($event->CurrentlyPlaying) array_push($event->eventItems, ' is playing');
            if($event->ListID != null) array_push($event->eventItems, ' <span class="label label-' . $event->ListStyle . '">' . $event->ListThirdPerson . '</span>');
            if($event->StatusID != null) array_push($event->eventItems, ' <span class="label label-' . $event->StatusStyle . '">' . $event->StatusThirdPerson . '</span>');

            // add platforms in collection
            $event->platforms = $this->Game->getGamesPlatformsInCollection($event->GBID, $event->UserID);
            
            // get comments
            $event->comments = $this->getCommentsForEvent($event->EventID, $DateTimeFormat);

            // format date stamp
            $event->DateStampFormatted = $this->Time->GetDateTimeInFormat($event->DateStamp, $DateTimeFormat);
        }

        return $events;
    }

    // add comment to event
    function addComment($eventID, $userID, $comment)
    {
        $data = array(
           'Comment' => $comment,
           'UserID' => $userID,
           'LinkID' => $eventID,
           'CommentTypeID' => 1,
           'DateStamp' => date("Y-m-d H:i:s")
        );

        return $this->db->insert('comments', $data); 
    }

    // get comments for event
    function getCommentsForEvent($eventID, $DateTimeFormat) 
    {
        $this->db->select('*');
        $this->db->from('comments');
        $this->db->join('users', 'comments.UserID = users.UserID');
        $this->db->where('comments.LinkID', $eventID); 
        $this->db->where('comments.CommentTypeID', 1); // UserEvents comment
        $this->db->order_by("DateStamp", "asc"); 
        $comments = $this->db->get()->result();

        // loop through events
        foreach ($comments as $comment)
        {
            // transform markdown to HTML
            $comment->Comment = $this->md->defaultTransform($comment->Comment);

            // format date stamp
            $comment->DateStampFormatted = $this->Time->GetDateTimeInFormat($comment->DateStamp, $DateTimeFormat);
        }

        return $comments;
    }
}