<?php
$count = collect(array_filter(explode(',', \Utilities::getSetting('sender_id_list'))))->count();
$senderIds = explode(',', \Utilities::getSetting('sender_id_list'));
?>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="name">Event name</label>
                <input type="text" name="name" value="{{ old('name', isset($event) ? $event->name : '') }}" class="form-control" id="name">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="date">Event date</label>
                <input type="text" name="date" value="{{ old('date', isset($event) && $event->date != "" ? $event->date->format('Y-m-d') : '') }}" class="form-control datepicker-default" id="date">
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="description">Event description</label>
                <input type="text" name="description" value="{{ old('description', isset($event) ? $event->description : '') }}" class="form-control" id="description">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            <label for="status">Status</label>
            <!--0 for inactive , 1 for active-->
                <select name="status" class="form-control selectpicker show-tick show-menu-arrow" id="status">
                    <option value="1" {{ old('status', isset($event) ? $event->status : '') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', isset($event) ? $event->status : '') == '0' ? 'selected' : '' }}>InActive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="send_to">Send to</label>
                <select name="send_to[]" class="form-control selectpicker show-tick show-menu-arrow" multiple="multiple" id="send_to">
                    <option value="0" {{ old('send_to', isset($event) && is_array($event->send_to) && in_array('0', $event->send_to) ? 'selected' : '') }}>Active members</option>
                    <option value="1" {{ old('send_to', isset($event) && is_array($event->send_to) && in_array('1', $event->send_to) ? 'selected' : '') }}>Inactive members</option>
                    <option value="2" {{ old('send_to', isset($event) && is_array($event->send_to) && in_array('2', $event->send_to) ? 'selected' : '') }}>Lead enquiries</option>
                    <option value="3" {{ old('send_to', isset($event) && is_array($event->send_to) && in_array('3', $event->send_to) ? 'selected' : '') }}>Lost enquiries</option>
                </select>
            </div>
        </div>
    </div>

    @if($count == 1)

        <input type="hidden" name="sender_id" value="{{ \Utilities::getSetting('sms_sender_id') }}">

    @elseif($count > 1)

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="sender_id">Sender Id</label>
                    <select id="sender_id" name="sender_id" class="form-control selectpicker show-tick">
                        @foreach($senderIds as $senderId)
                            <option value="{{ $senderId }}">{{ $senderId }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="message">Message text</label>
                <textarea name="message" class="form-control" id="message" rows="5">{{ old('message', isset($event) ? $event->message : '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">{{ $submitButtonText }}</button>
            </div>
        </div>
    </div>
</div>
                            