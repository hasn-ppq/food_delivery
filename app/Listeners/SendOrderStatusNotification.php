<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\User;
use App\Services\FcmService;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;
use App\Notifications\OrderStatusDatabaseNotification;
class SendOrderStatusNotification
{
    public function __construct(
        protected FcmService $fcm
    ) {}

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $status = $event->newStatus;

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ إشعارات صاحب المطعم
        |--------------------------------------------------------------------------
        */
        if (in_array($status, ['pending', 'canceled','ready_to_receive','cooking'])) {
           $owner = User::whereHas('role', function ($q) {
        $q->where('slug', 'owner');
    })
    ->where('id', $order->restaurant->owner_id ?? null)
    ->first();


            if ($owner) {
               FilamentNotification::make()
                ->title('تحديث طلب')
                ->body("الطلب #{$order->id} حالته: {$status}")
                ->actions([
               Action::make('open')
                ->label('فتح الطلب')
                ->url(
                route('filament.restaurant.resources.orders.index', $order)
                 )->markAsRead(),
             ])
              ->sendToDatabase($owner, isEventDispatched: true);
   
                $owner->notify(
                    new OrderStatusDatabaseNotification($order, $status)
);

            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ إشعارات الدلفري (حسب المسافة)
        |--------------------------------------------------------------------------
        */
        if ($status === 'ready_to_receive' && is_null($order->delivery_id)) {

           $deliveryUsers = User::whereHas('role', function ($q) {
        $q->where('slug', 'delivery');
    })
    ->where('is_active', true)
    ->get();

            foreach ($deliveryUsers as $delivery) {

                if (! $delivery->lat || ! $delivery->lng) {
                    continue;
                }

                $distance = $this->distanceKm(
                    $delivery->lat,
                    $delivery->lng,
                    $order->restaurant->lat,
                    $order->restaurant->lng
                );

                if ($distance <= 3) {
                     FilamentNotification::make()
                      ->title('تحديث طلب')
                      ->body("الطلب #{$order->id} حالته: {$status}")
                      ->actions([
                     Action::make('open')
                      ->label('فتح الطلب')
                      ->url(
                       route(' filament.driver.resources.ready-orders.index', $order)
                       )->markAsRead(),
                     ])
                      ->sendToDatabase($owner, isEventDispatched: true);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ إشعارات الكاستمر (Database + FCM)
        |--------------------------------------------------------------------------
        */
        if (in_array($status, [
            'accepted',
            'cooking',
            'on_the_way',
            'canceled',
            'delivered'
        ])) {

            $customer = $order->customer;

            if ($customer) {
                // FCM (للتطبيق)
                $this->fcm->send(
                    $customer->device_token,
                    'تحديث الطلب',
                    "طلبك #{$order->id} أصبح: {$status}",
                    [
                        'order_id' => (string) $order->id,
                        'status'   => $status,
                    ]
                );
            }
        }
    }

    /**
     * حساب المسافة بالكيلومتر (Haversine)
     */
    private function distanceKm($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLng / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
