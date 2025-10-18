<section>
  <div class="container mb-3">
    <div class="row">
      <div class="col-md-5 pl-1">
        <div class="well well-sm text-muted">
          <p class="lead text-center mb-1">@lang('theme.why_feedback_important')</p>
          <p class="my-2">@lang('theme.help.be_honest_when_leave_feedbacks')</p>
        </div>

        <p class="lead section-title mt-4">@lang('theme.section_headings.give_feedbacks_to_seller')</p>

        <div class="table-responsive">
          <table class="table bg-light" id="buyer-order-table" name="buyer-order-table">
            <tbody>
              <tr class="order-info-head">
                <td>
                  <h5 class="text-center"><span>@lang('theme.seller')</span></h5>
                </td>
              </tr>
              <tr class="order-body">
                <td>
                  <div class="product-img-wrap">
                    <img src="{{ get_storage_file_url(optional($order->shop->image)->path, 'small') }}" alt="{{ $order->shop->slug }}" title="{{ $order->shop->slug }}" />
                  </div>

                  <div class="product-info">
                    <a href="{{ route('show.store', $order->shop->slug) }}" class="product-info-title">
                      {{ $order->shop->name }}
                    </a>

                    <div class="mt-2">
                      @if (optional($order->shop->avgFeedback)->rating)
                        @include('theme::layouts.ratings', ['ratings' => $order->shop->avgFeedback->rating, 'count' => $order->shop->avgFeedback->count])
                      @else
                        <span class="text-muted small">@lang('theme.no_reviews')</span>
                      @endif
                    </div>
                  </div>
                </td>
              </tr> <!-- /.order-body -->

              <tr class="order-info-head">
                <td>
                  <p>@lang('theme.how_satisfied_you_are')</p>
                </td>
              </tr>

              <tr class="order-body">
                <td>
                  <div class="post-review-box pb-5">
                    @if ($order->feedback)
                      @include('theme::layouts.ratings', ['ratings' => $order->feedback->rating])
                      <p>
                        {{ $order->feedback->comment != '' ? $order->feedback->comment : trans('theme.no_comment') }}
                      </p>
                    @else
                      {!! Form::open(['route' => ['shop.feedback', $order], 'files' => true, 'data-toggle' => 'validator']) !!}
                      <div class="product-info-rating feedback-stars">
                        <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.hate_it')" data-value="1">
                          <i class="fas fa-star fa-fw"></i>
                        </span>
                        <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.not_so_good')" data-value="2">
                          <i class="fas fa-star fa-fw"></i>
                        </span>
                        <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.its_ok')" data-value="3">
                          <i class="fas fa-star fa-fw"></i>
                        </span>
                        <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.like_it')" data-value="4">
                          <i class="fas fa-star fa-fw"></i>
                        </span>
                        <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.love_it')" data-value="5">
                          <i class="fas fa-star fa-fw"></i>
                        </span>
                        <span class="response small text-primary">@lang('theme.love_it')</span>
                        {{ Form::hidden('rating', 5, ['class' => 'rating-value']) }}
                      </div> <!-- /.feedback-stars -->

                      <div class="form-group">
                        {{ Form::textarea('comment', null, ['rows' => '2', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.write_your_feedback'), 'minlength' => '10', 'maxlength' => '250']) }}
                        <div class="help-block with-errors"></div>
                      </div>

                      <button class="confirm btn btn-primary rounded-0 col-md-4" data-confirm="@lang('theme.confirm_action.cant_undo')" type="submit">@lang('theme.button.save')</button>
                      {!! Form::close() !!}
                    @endif
                  </div> <!-- /.post-review-box -->
                </td>
              </tr> <!-- /.order-body -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-7 pr-1">
        <p class="lead section-title">@lang('theme.section_headings.give_feedbacks_to_products')</p>

        {!! Form::open(['route' => ['save.feedback', $order], 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
        @php
          $feedback_given = true;
        @endphp
        <div class="table-responsive">
          <table class="table" id="buyer-order-table" name="buyer-order-table">
            <tbody>
              <tr class="order-info-head">
                <td>
                  <h5 class="text-center"><span>@lang('theme.product')</span></h5>
                </td>
                <td>
                  <h5><span>@lang('theme.how_was_the_product')</span></h5>
                </td>
              </tr>

              @foreach ($order->inventories as $item)
                <tr class="order-body">
                  <td>
                    <div class="product-img-wrap">
                      <img src="{{ get_inventory_img_src($item, 'small') }}" alt="{{ $item->slug }}" title="{{ $item->slug }}" />
                    </div>

                    <div class="product-info">
                      <a href="{{ route('show.product', $item->slug) }}" class="product-info-title">{{ $item->pivot->item_description }}</a>

                      <div class="mt-2">
                        @if (optional($item->avgFeedback)->rating)
                          @include('theme::layouts.ratings', ['ratings' => $item->avgFeedback->rating, 'count' => $item->avgFeedback->count])
                        @else
                          <span class="text-muted small">@lang('theme.no_reviews')</span>
                        @endif
                      </div>
                    </div>
                  </td>

                  <td>
                    @if ($item->pivot->feedback_id)
                      @php
                        $feedback = \App\Models\Feedback::find($item->pivot->feedback_id);
                      @endphp

                      @include('theme::layouts.ratings', ['ratings' => $feedback->rating])

                      <p>
                        {{ $feedback->comment != '' ? $feedback->comment : trans('theme.no_comment') }}
                      </p>
                    @else
                      @php
                        $feedback_given = false;
                      @endphp

                      <div class="post-review-box">
                        <div class="product-info-rating feedback-stars">
                          <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.hate_it')" data-value="1">
                            <i class="fas fa-star fa-fw"></i>
                          </span>
                          <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.not_so_good')" data-value="2">
                            <i class="fas fa-star fa-fw"></i>
                          </span>
                          <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.its_ok')" data-value="3">
                            <i class="fas fa-star fa-fw"></i>
                          </span>
                          <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.like_it')" data-value="4">
                            <i class="fas fa-star fa-fw"></i>
                          </span>
                          <span class="star rated" data-toggle="tooltip" data-title="@lang('theme.love_it')" data-value="5">
                            <i class="fas fa-star fa-fw"></i>
                          </span>
                          <span class="response small text-primary">@lang('theme.love_it')</span>
                          {{ Form::hidden('items[' . $item->pivot->inventory_id . '][rating]', 5, ['class' => 'rating-value']) }}
                        </div> <!-- /.feedback-stars -->

                        <div class="form-group">
                          {{ Form::textarea('items[' . $item->pivot->inventory_id . '][comment]', null, ['rows' => '2', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.write_your_feedback'), 'minlength' => 10, 'maxlength' => 250]) }}
                          <div class="help-block with-errors"></div>
                        </div>
                      </div> <!-- /.post-review-box -->
                    @endif
                  </td>
                </tr> <!-- /.order-body -->
              @endforeach
              <tr>
                <td>
                  @if (!$feedback_given && $order->inventories->count() > 1)
                    <p class="text-muted text-info small"><i class="fas fa-info-circle"></i> @lang('theme.help.give_all_feedbacks_together')</p>
                  @endif
                </td>

                <td>
                  @if ($feedback_given)
                    <p class="text-muted">@lang('theme.notify.your_feedback_saved')</p>
                  @else
                    <button class="confirm btn btn-info btn-block rounded-0" data-confirm="@lang('theme.confirm_action.cant_undo')" type="submit">@lang('theme.button.save')</button>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</section>
<!-- END CONTENT SECTION -->
