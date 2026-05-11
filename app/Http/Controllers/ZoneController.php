<?php

namespace App\Http\Controllers;

use App\Http\Requests\Zones\StoreZoneMemberRequest;
use App\Http\Requests\Zones\StoreZoneRequest;
use App\Http\Requests\Zones\UpdateZoneRequest;
use App\Models\Leader;
use App\Models\Member;
use App\Models\Zone;
use App\Models\ZoneMember;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));

        $zones = Zone::query()
            ->with('leader')
            ->withCount('memberships')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('zones.index', [
            'zones' => $zones,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('zones.create', [
            'zone' => new Zone(['status' => 'active']),
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function store(StoreZoneRequest $request): RedirectResponse
    {
        $zone = Zone::query()->create($request->validated());

        $this->auditLogger->log(
            request: $request,
            action: 'zone.create',
            entityType: 'zone',
            entityId: $zone->id,
            after: Arr::only($zone->toArray(), ['name', 'leader_id', 'status']),
        );

        return redirect()->route('zones.show', $zone)->with('status', 'Zone created successfully.');
    }

    public function show(Zone $zone): View
    {
        $zone->load([
            'leader',
            'memberships.member',
        ]);

        return view('zones.show', [
            'zone' => $zone,
            'availableMembers' => Member::query()
                ->whereNotIn('id', $zone->memberships->pluck('member_id'))
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'phone', 'zone']),
        ]);
    }

    public function edit(Zone $zone): View
    {
        return view('zones.edit', [
            'zone' => $zone,
            'leaders' => Leader::query()->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function update(UpdateZoneRequest $request, Zone $zone): RedirectResponse
    {
        $before = Arr::only($zone->toArray(), ['name', 'leader_id', 'status', 'description']);
        $previousName = $zone->name;
        $zone->update($request->validated());

        if ($previousName !== $zone->name) {
            Member::query()->where('zone', $previousName)->update(['zone' => $zone->name]);
        }

        $this->auditLogger->log(
            request: $request,
            action: 'zone.update',
            entityType: 'zone',
            entityId: $zone->id,
            before: $before,
            after: Arr::only($zone->fresh()->toArray(), ['name', 'leader_id', 'status', 'description']),
        );

        return redirect()->route('zones.show', $zone)->with('status', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        Member::query()->where('zone', $zone->name)->update(['zone' => null]);

        $before = Arr::only($zone->toArray(), ['name', 'leader_id', 'status']);
        $zoneId = $zone->id;
        $zone->delete();

        $this->auditLogger->log(
            request: request(),
            action: 'zone.delete',
            entityType: 'zone',
            entityId: $zoneId,
            before: $before,
        );

        return redirect()->route('zones.index')->with('status', 'Zone deleted successfully.');
    }

    public function storeMember(StoreZoneMemberRequest $request, Zone $zone): RedirectResponse
    {
        $data = $request->validated();

        if ($zone->memberships()->where('member_id', $data['member_id'])->exists()) {
            throw ValidationException::withMessages([
                'member_id' => 'This member already belongs to the zone.',
            ]);
        }

        $membership = $zone->memberships()->create([
            'member_id' => $data['member_id'],
            'status' => $data['status'],
            'joined_at' => now(),
        ]);

        Member::query()->whereKey($data['member_id'])->update(['zone' => $zone->name]);

        $this->auditLogger->log(
            request: $request,
            action: 'zone.member.add',
            entityType: 'zone_member',
            entityId: $membership->id,
            after: [
                'zone_id' => $zone->id,
                'member_id' => $membership->member_id,
                'status' => $membership->status,
            ],
        );

        return redirect()->route('zones.show', $zone)->with('status', 'Member assigned to zone.');
    }

    public function destroyMember(Zone $zone, ZoneMember $membership): RedirectResponse
    {
        abort_unless($membership->zone_id === $zone->id, 404);

        $member = $membership->member;
        $before = Arr::only($membership->toArray(), ['zone_id', 'member_id', 'status']);
        $membershipId = $membership->id;
        $membership->delete();

        if ($member && $member->zone === $zone->name) {
            $member->update(['zone' => null]);
        }

        $this->auditLogger->log(
            request: request(),
            action: 'zone.member.remove',
            entityType: 'zone_member',
            entityId: $membershipId,
            before: $before,
        );

        return redirect()->route('zones.show', $zone)->with('status', 'Member removed from zone.');
    }
}
