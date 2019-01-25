<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelController;
use \Illuminate\Support\Collection;
use App\City;
use App\Hotel;
use App\Room;
use Cart;
use App\Receipt;

class RoomReservationController extends Controller
{
    public function searchRooms() {
        $cities = City::all();
        $hotels = Hotel::all();
        $rooms = Room::all();
        $roomTypes = RoomController::searchRoomTypes();
        return view('reserveHotel', compact('cities','hotels','roomTypes'));
    }

    public function chooseHotelRoom(Request $request){
        $user_id = Auth::id();
        Cart::session($user_id);

        $city_name = $request->input('city');
        $city_id = City::all()->where('city_name', $city_name)->first()->id;
        $stars = $request->input('stars');
        $room_type = $request->input('room_type');
        $check_in = $request->input('check_in');
        $check_out = $request->input('check_out');

        Cart::add(array(
            'id' => 0,
            'name' => 'aux',
            'price' => 0,
            'quantity' => 1,
            'attributes' => array(['check_in' => $check_in,
                                   'check_out' => $check_out])
        ));

        $city_hotels = Hotel::all()->where('city_id', $city_id);
        $city_hotels = $city_hotels->where('hotel_stars', $stars);

        $availableRooms = Collection::make();
        foreach ($city_hotels as $hotel){
            $hotel_available_rooms = HotelController::showAvailableRooms($hotel->id, $check_in, $check_out);
            foreach ($hotel_available_rooms as $room){
                $availableRooms->push($room);
            }
        }
        return view('chooseRoom', compact('availableRooms'));
    }

    public function storeRoomReservation($id){
        $user_id = Auth::id();
        Cart::session($user_id);

        $check_in = Cart::get(0)->attributes['check_in'];
        $check_out = Cart::get(0)->attributes['check_out'];

        $room_id = $id;

        // Create the reservation
        $reservation = new Reservation;
        $reservation->reservation_ip = "test";
        $reservation->reservation_date = date('Y-m-d H:i:s');
        $reservation->save();
        
        // Create a receipt and link it with the logged user
        $receipt = new Receipt;
        $receipt->receipt_date = date('Y-m-d H:i:s');
        $receipt->receipt_type = "boleta";
        $receipt->receipt_ammount = Room::find($room_id)->room_price;
        $receipt->user_id = $user_id;
        $receipt->reservation_id = $reservation->id;
        $receipt->save();

        $reservation->rooms()->attach($room_id, ['reservation_room_lease' => $check_in,
                                                         'reservation_room_return' => $check_out]);

        return redirect('/reserve/rooms/pay');
    }


    public function pay(){
        $user_id = Auth::user()->id;; // El usuario loggeado
        Cart::session($user_id);
        $total = 0;
        foreach (Cart::getContent() as $ticket){
            if ($ticket->id != 0){
                $total += $ticket->price;
            }
        }
        // Asignar el total al recibo de esta compra
        // $recibo = Receipt::find(Cart::get(0)->price);
        // $recibo->receipt_ammount = $total;
        // $recibo->save();
        return view('pay', compact('total'));
    }
}
