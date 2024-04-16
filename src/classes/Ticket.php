<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Ticket
{
    /**
     * @param $userType
     * @return stdClass
     */
    public static function getDepartmentsList()
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_departments 
        where department_status = :status order by department_priority asc";
        $params = [
            'status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $departmentList = [];
            foreach ($result->response as $item) {
                $department = new stdClass();
                $department->DepartmentId = $item->department_id;
                $department->DepartmentName = array_column(json_decode($item->department_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($departmentList, $department);
            }
            $response = sendResponse(200, '', $departmentList);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $departmentId
     * @param $subject
     * @param $message
     * @param $token
     * @param array $attachments
     * @param bool $isSystem
     * @return stdClass
     */
    public static function createTicket($userId, $departmentId, $subject, $message, $token, $attachments = [], $isSystem = false)
    {
        if (!Security::verifyCSRF2($token , false) ){
            return sendResponse(-10, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF2();

        $sql = "insert into tbl_tickets(user_id, department_id, ticket_title, ticket_status, ticket_submit_date) 
        VALUES (:userId, :departmentId, :subject, :status, :time);";
        $params = [
            'userId' => $userId,
            'departmentId' => $departmentId,
            'subject' => $subject,
            'status' => 'open',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = self::sendTicketMessage($userId, $result->response, $message, null, $attachments, true);
            User::createUserLog($userId, 'uLog_create_ticket', 'ticket');
        } else {
            $response = sendResponse(-10, 'Ticket creation error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $ticketId
     * @param $message
     * @param $token
     * @param $attachments
     * @param bool $isSystem
     * @return stdClass
     */
    public static function sendTicketMessage($userId, $ticketId, $message, $token, $attachments = [], $isSystem = false)
    {
        if (!Security::verifyCSRF('send-ticket-message', $token) && !$isSystem) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('');

        $sql = "insert into tbl_ticket_messages(ticket_id, message_body, message_attachment, message_status, message_submit_date) 
        VALUES (:ticketId, :message, :attachments, :status, :time);";
        $params = [
            'ticketId' => $ticketId,
            'message' => $message,
            'attachments' => json_encode($attachments),
            'status' => 'unread',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Message sent', $csrf);
            User::createUserLog($userId, 'uLog_send_ticket_message', 'ticket');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @return stdClass
     */
    public static function getMyTicketList($userId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select ticket_id, department_name, ticket_title, ticket_status, ticket_submit_date
        from tbl_tickets 
        inner join tbl_departments on tbl_tickets.department_id = tbl_departments.department_id
        where user_id = :userId
        order by ticket_id desc;";
        $params = [
            'userId' => $userId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $ticketList = [];
            foreach ($result->response as $item) {
                $ticket = new stdClass();
                $ticket->TicketId = $item->ticket_id;
                $ticket->TicketDepartment = array_column(json_decode($item->department_name), 'value', 'slug')[$_COOKIE['language']];
                $ticket->TicketTitle = $item->ticket_title;
                $ticket->TicketStatus = $item->ticket_status;
                $ticket->TicketTime = $item->ticket_submit_date;

                array_push($ticketList, $ticket);
            }
            $response = sendResponse(200, '', $ticketList);
        }
        return $response;
    }


    /**
     * @param $ticketId
     * @param $userId
     * @return stdClass
     */
    public static function getTicketDetail($ticketId, $userId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select ticket_id, department_name, ticket_title, ticket_status, ticket_submit_date
        from tbl_tickets 
        inner join tbl_departments on tbl_tickets.department_id = tbl_departments.department_id
        where user_id = :userId and ticket_id = :ticketId;";
        $params = [
            'userId' => $userId,
            'ticketId' => $ticketId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $ticket = new stdClass();
            foreach ($result->response as $item) {
                $ticket->TicketId = $item->ticket_id;
                $ticket->TicketDepartment = array_column(json_decode($item->department_name), 'value', 'slug')[$_COOKIE['language']];
                $ticket->TicketTitle = $item->ticket_title;
                $ticket->TicketStatus = $item->ticket_status;
                $ticket->TicketTime = $item->ticket_submit_date;
            }
            $response = sendResponse(200, '', $ticket);
        }
        return $response;
    }


    /**
     * @param $ticketId
     * @return stdClass
     */
    public static function getTicketMessages($ticketId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select * 
        from tbl_ticket_messages
        where ticket_id = :ticketId
        order by message_id";
        $params = [
            'ticketId' => $ticketId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $messageList = [];
            foreach ($result->response as $item) {
                $message = new stdClass();
                $message->MessageId = $item->message_id;
                $message->isAdmin = (!empty($item->admin_id)) ? true : false;
                $message->MessageText = $item->message_body;
                $message->MessageAttachment = json_decode($item->message_attachment);
                $message->MessageStatus = $item->message_status;
                $message->MessageTime = $item->message_submit_date;

                array_push($messageList, $message);
            };
            $response = sendResponse(200, '', $messageList);
        }
        return $response;
    }
}