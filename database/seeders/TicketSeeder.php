<?php
// database/seeders/TicketSeeder.php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\Message;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {

        $users = User::where('role', UserRole::CLIENT)->get();

        // Client Tickets (Authenticated Users)
        $this->createClientTickets($users);

        // Guest Tickets (Contact Form Submissions)
        $this->createGuestTickets();
    }

    private function createClientTickets($users)
    {
        // Ticket 1: Booking Issue - NEW
        $ticket1 = Ticket::create([
            'subject' => 'Unable to complete booking for BMW X5',
            'status' => TicketStatus::NEW,
            'user_id' => $users[0]->id,
        ]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'message' => 'Hi, I\'ve been trying to book a BMW X5 for next week but the payment keeps failing. I\'ve tried multiple credit cards and they all seem to work fine on other websites. Can you please help me with this issue?',
            'is_admin' => false,
        ]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'message' => 'Hello! Thank you for contacting us. I\'m sorry to hear about the payment issue. Can you please provide me with the approximate time when you tried to make the booking and the last 4 digits of the card you were using? This will help us investigate the issue.',
            'is_admin' => true,
        ]);

        Message::create([
            'ticket_id' => $ticket1->id,
            'message' => 'I tried around 3 PM today. The card ending in 4567. I also tried another card ending in 1234. Both failed at the payment step.',
            'is_admin' => false,
        ]);

        // Ticket 2: Car Damage Report - In Progress
        $ticket2 = Ticket::create([
            'subject' => 'Minor scratch on returned vehicle - Toyota Camry',
            'status' => TicketStatus::IN_PROGRESS,
            'user_id' => $users[1]->id,
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'message' => 'I returned my rental car (Toyota Camry, booking #RC-2024-001) yesterday and there was a minor scratch on the rear bumper that I don\'t think was there when I picked it up. I\'m concerned about being charged for damage I didn\'t cause.',
            'is_admin' => false,
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'message' => 'Thank you for bringing this to our attention. I\'ve located your booking and I\'m currently reviewing the pre-rental inspection photos. I\'ll compare them with the return inspection and get back to you within 24 hours.',
            'is_admin' => true,
        ]);

        Message::create([
            'ticket_id' => $ticket2->id,
            'message' => 'Thank you for looking into this. I appreciate your prompt response.',
            'is_admin' => false,
        ]);

        // Ticket 3: Account Access Issue - CLOSED
        $ticket3 = Ticket::create([
            'subject' => 'Cannot access my account after password reset',
            'status' => TicketStatus::CLOSED,
            'user_id' => $users[2]->id,
            'resolved_at' => Carbon::now()->subDays(2),
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'message' => 'I reset my password but I still can\'t log into my account. The system says my credentials are invalid.',
            'is_admin' => false,
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'message' => 'I can help you with that. Let me check your account status. Can you confirm the email address associated with your account?',
            'is_admin' => true,
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'message' => 'Yes, it\'s ' . $users[2]->email,
            'is_admin' => false,
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'message' => 'I found the issue. Your account was temporarily locked due to multiple failed login attempts. I\'ve unlocked it and sent you a new temporary password. Please try logging in now and change your password once you\'re in.',
            'is_admin' => true,
        ]);

        Message::create([
            'ticket_id' => $ticket3->id,
            'message' => 'Perfect! I was able to log in successfully. Thank you for your quick help!',
            'is_admin' => false,
        ]);

        // Ticket 4: Billing Inquiry - NEW
        $ticket4 = Ticket::create([
            'subject' => 'Unexpected charge on my credit card',
            'status' => TicketStatus::NEW,
            'user_id' => $users[3]->id,
        ]);

        Message::create([
            'ticket_id' => $ticket4->id,
            'message' => 'I see a charge of $75 on my credit card that I don\'t recognize. It\'s from your company and dated last week. My last rental was over a month ago. Can you explain what this charge is for?',
            'is_admin' => false,
        ]);

        // Ticket 5: Vehicle Request - In Progress
        $ticket5 = Ticket::create([
            'subject' => 'Special vehicle request for wedding',
            'status' => TicketStatus::IN_PROGRESS,
            'user_id' => $users[4]->id,
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'message' => 'Hi! I\'m getting married next month and I\'m looking for a luxury car for the wedding day. Do you have any Bentley or Rolls Royce vehicles available for rent? The wedding is on March 15th.',
            'is_admin' => false,
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'message' => 'Congratulations on your upcoming wedding! Let me check our luxury fleet availability for March 15th. We do have some premium vehicles, though I\'ll need to confirm specific models. What time would you need the vehicle and for how long?',
            'is_admin' => true,
        ]);

        Message::create([
            'ticket_id' => $ticket5->id,
            'message' => 'Thank you! I would need it from 10 AM to around 8 PM. The ceremony is at 2 PM and reception ends around 7 PM.',
            'is_admin' => false,
        ]);
    }

    private function createGuestTickets()
    {
        // Guest Ticket 1: General Inquiry - NEW
        $guestTicket1 = Ticket::create([
            'subject' => 'Group booking inquiry for corporate event',
            'status' => TicketStatus::NEW,
            'guest_name' => 'Sarah Johnson',
            'guest_email' => 'sarah.johnson@techcorp.com',
        ]);

        Message::create([
            'ticket_id' => $guestTicket1->id,
            'message' => 'Hello, I\'m organizing a corporate event for 50 attendees and we need transportation. We would need about 10-12 vehicles for a weekend in April. Do you offer group discounts? What would be the best way to coordinate this?',
            'is_admin' => false,
        ]);

        // Guest Ticket 2: Pricing Question - CLOSED
        $guestTicket2 = Ticket::create([
            'subject' => 'Weekly rates for SUV rental',
            'status' => TicketStatus::CLOSED,
            'guest_name' => 'Mike Rodriguez',
            'guest_email' => 'mike.r.email@gmail.com',
            'resolved_at' => Carbon::now()->subDays(1),
        ]);

        Message::create([
            'ticket_id' => $guestTicket2->id,
            'message' => 'Hi, I\'m planning a family vacation and need an SUV for a week. What are your weekly rates and do you offer any discounts for week-long rentals?',
            'is_admin' => false,
        ]);

        // Guest Ticket 3: Location Inquiry - NEW
        $guestTicket3 = Ticket::create([
            'subject' => 'Airport pickup availability',
            'status' => TicketStatus::NEW,
            'guest_name' => 'Emily Chen',
            'guest_email' => 'emily.chen.traveler@outlook.com',
        ]);

        Message::create([
            'ticket_id' => $guestTicket3->id,
            'message' => 'Do you have a pickup location at LAX airport? I\'m flying in next Tuesday at 6 PM and need a car immediately after landing. Also, what documents do I need to bring for the rental?',
            'is_admin' => false,
        ]);

        // Guest Ticket 4: Complaint - In Progress
        $guestTicket4 = Ticket::create([
            'subject' => 'Poor customer service experience',
            'status' => TicketStatus::IN_PROGRESS,
            'guest_name' => 'Robert Thompson',
            'guest_email' => 'r.thompson.feedback@yahoo.com',
        ]);

        Message::create([
            'ticket_id' => $guestTicket4->id,
            'message' => 'I tried calling your customer service line three times yesterday and was put on hold for over 30 minutes each time before I gave up. I have a simple question about extending my current rental but can\'t get through to anyone. This is very frustrating.',
            'is_admin' => false,
        ]);

        // Guest Ticket 5: Special Requirements - NEW
        $guestTicket5 = Ticket::create([
            'subject' => 'Wheelchair accessible vehicle needed',
            'status' => TicketStatus::NEW,
            'guest_name' => 'Lisa Martinez',
            'guest_email' => 'lisa.martinez.family@gmail.com',
        ]);

        Message::create([
            'ticket_id' => $guestTicket5->id,
            'message' => 'Hello, I need to rent a wheelchair accessible vehicle for my father who uses a wheelchair. Do you have such vehicles available? We need it for a week starting February 20th. Please let me know what options you have and the pricing.',
            'is_admin' => false,
        ]);
    }
}
