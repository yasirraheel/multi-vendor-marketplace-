<?php

namespace App\Repositories\Shop;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Events\Shop\ShopDeleted;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;

class EloquentShop extends EloquentRepository implements BaseRepository, ShopRepository
{
    protected $model;

    public function __construct(Shop $shop)
    {
        $this->model = $shop;
    }

    public function all()
    {
        return $this->model->with(
            'config',
            'owner.avatarImage:path,imageable_id,imageable_type',
            'subscriptions',
            'currentSubscription',
            'plan:name,plan_id',
            'logoImage',
            'primaryAddress'
        )->get();
    }

    public function trashOnly()
    {
        return $this->model->with('logo')->onlyTrashed()->get();
    }

    public function staffs($shop)
    {
        return $shop->staffs()->with('role', 'primaryAddress')->get();
    }

    public function staffsTrashOnly($shop)
    {
        return $shop->staffs()->onlyTrashed()->get();
    }

    public function update(Request $request, $id)
    {
        $shop = parent::update($request, $id);

        if ($request->hasFile('logo') || ($request->input('delete_logo') == 1)) {
            $shop->deleteLogo();
        }

        if ($request->hasFile('logo')) {
            $shop->saveImage($request->file('logo'), 'logo');
        }

        if ($request->hasFile('cover_image') || ($request->input('delete_cover_image') == 1)) {
            $shop->deleteCoverImage();
        }

        if ($request->hasFile('cover_image')) {
            $shop->saveImage($request->file('cover_image'), 'cover');
        }

        return $shop;
    }

    public function destroy($id)
    {
        $shop = parent::findTrash($id);

        $shop->clearData();

        return $shop->forceDelete();
    }

    // public function saveAdrress(array $address, $shop)
    // {
    //     $shop->addresses()->create($address);
    // }

    public function massTrash($ids)
    {
        $shops = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($shops as $shop) {
            $shop->owner()->delete();
            $shop->staffs()->delete();

            event(new ShopDeleted($shop->id));
        }

        return parent::massTrash($ids);
    }

    public function massDestroy($ids)
    {
        $shops = $this->model->withTrashed()->whereIn('id', $ids)->get();

        foreach ($shops as $shop) {
            $shop->clearData();
        }

        return parent::massDestroy($ids);
    }

    public function emptyTrash()
    {
        $shops = $this->model->onlyTrashed()->get();

        foreach ($shops as $shop) {
            $shop->clearData();
        }

        return parent::emptyTrash();
    }

    public function deleteStaff($shop)
    {
        $staffs = $shop->staffs();

        foreach ($staffs as $staff) {
            $staff->flushAddresses();
            $staff->flushImages();
        }

        return $staffs->forceDelete();
    }
}
