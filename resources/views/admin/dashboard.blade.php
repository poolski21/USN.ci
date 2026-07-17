@extends('layouts.app')

@section('title', 'Tableau de bord administrateur')

@section('content')
<div class="mx-auto max-w-7xl py-8">
  <div class="mb-6 rounded-3xl border border-[#D4CABC] bg-white p-8 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-[#1F2E26]">Tableau de bord administrateur</h1>
        <p class="mt-2 text-sm text-[#5E6E52]">Bonjour {{ $user->name }}, voici les statistiques de traçabilité en temps réel.</p>
      </div>
      <div class="rounded-3xl border border-[#D4CABC] bg-[#F8F2E6] px-5 py-4 text-sm text-[#5E6E52]">
        <span class="font-semibold text-[#1F2E26]">Role :</span> {{ ucfirst($user->role ?? 'admin') }}
      </div>
    </div>
  </div>

  <div class="grid gap-4 xl:grid-cols-[1.5fr_1fr]">
    <div class="space-y-4">
      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Utilisateurs totaux</p>
          <p id="stat-total-users" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Utilisateurs actifs</p>
          <p id="stat-active-users" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($activeUsers) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Publications</p>
          <p id="stat-total-posts" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalPosts) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Commentaires</p>
          <p id="stat-total-comments" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalComments) }}</p>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Partages</p>
          <p id="stat-total-shares" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalShares) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Messages</p>
          <p id="stat-total-messages" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalMessages) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Groupes</p>
          <p id="stat-total-groups" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($totalGroups) }}</p>
        </div>
        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
          <p class="text-sm text-[#5E6E52]">Activités récentes</p>
          <p id="stat-latest-activities" class="mt-3 text-3xl font-bold text-[#1F2E26]">{{ number_format($latestActivities->count()) }}</p>
        </div>
      </div>

      <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-[#1F2E26]">Activité des 14 derniers jours</h2>
            <p class="mt-2 text-sm text-[#5E6E52]">Nombre de logs enregistrés par jour.</p>
          </div>
        </div>
        <div class="mt-6">
          <canvas id="activity-chart" width="100" height="40"></canvas>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1F2E26]">Top 5 utilisateurs par traçabilité</h2>
        <div class="mt-4 space-y-3">
          @foreach($topUsers as $topUser)
            <div class="flex items-center justify-between rounded-3xl border border-[#E7E3D9] bg-[#FBF8F1] px-4 py-3">
              <div>
                <p class="font-semibold text-[#1F2E26]">{{ $topUser->name }}</p>
                <p class="text-sm text-[#5E6E52]">{{ $topUser->activities_count }} logs</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1F2E26]">Utilisateurs par filière</h2>
        <div class="mt-4 space-y-3">
          @forelse($usersByFiliere as $row)
            <div class="flex items-center justify-between rounded-3xl border border-[#E7E3D9] bg-[#FBF8F1] px-4 py-3">
              <span class="text-sm text-[#1F2E26]">{{ $row->filiere ?: 'Non renseigné' }}</span>
              <span class="font-semibold text-[#1F2E26]">{{ $row->total }}</span>
            </div>
          @empty
            <p class="text-sm text-[#5E6E52]">Aucune donnée de filière disponible.</p>
          @endforelse
        </div>
      </div>

      <div class="rounded-3xl border border-[#D4CABC] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#1F2E26]">Dernières actions enregistrées</h2>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full divide-y divide-[#E7E3D9]">
            <thead class="bg-[#F8F2E6] text-left text-xs uppercase tracking-wide text-[#5E6E52]">
              <tr>
                <th class="px-4 py-3">Utilisateur</th>
                <th class="px-4 py-3">Action</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E7E3D9] bg-white text-sm text-[#5E6E52]">
              @foreach($latestActivities as $activity)
                <tr>
                  <td class="px-4 py-3">{{ $activity->user->name ?? 'Utilisateur supprimé' }}</td>
                  <td class="px-4 py-3">{{ $activity->action }}</td>
                  <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($activity->description, 80) }}</td>
                  <td class="px-4 py-3">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const activityChart = document.getElementById('activity-chart');
    if (!activityChart) {
      return;
    }

    const chartData = {
      labels: @json($activityChart->pluck('date')->toArray()),
      datasets: [{
        label: 'Logs d’activité',
        data: @json($activityChart->pluck('total')->toArray()),
        backgroundColor: 'rgba(56, 161, 105, 0.18)',
        borderColor: 'rgba(34, 197, 94, 0.9)',
        borderWidth: 2,
        fill: true,
        tension: 0.35,
        pointRadius: 4,
        pointHoverRadius: 6,
      }]
    };

    const chart = new Chart(activityChart, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (context) {
                return context.parsed.y + ' actions';
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#4B5563' }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.15)' },
            ticks: { color: '#4B5563' }
          }
        }
      }
    });

    const refreshStats = async function () {
      try {
        const response = await fetch('{{ route('admin.dashboard.stats') }}', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
        });

        if (!response.ok) {
          return;
        }

        const data = await response.json();

        document.getElementById('stat-total-users').textContent = Number(data.totalUsers).toLocaleString();
        document.getElementById('stat-active-users').textContent = Number(data.activeUsers).toLocaleString();
        document.getElementById('stat-total-posts').textContent = Number(data.totalPosts).toLocaleString();
        document.getElementById('stat-total-comments').textContent = Number(data.totalComments).toLocaleString();
        document.getElementById('stat-total-shares').textContent = Number(data.totalShares).toLocaleString();
        document.getElementById('stat-total-messages').textContent = Number(data.totalMessages).toLocaleString();
        document.getElementById('stat-total-groups').textContent = Number(data.totalGroups).toLocaleString();
        document.getElementById('stat-latest-activities').textContent = Number(data.activityChart.reduce((sum, row) => sum + Number(row.total), 0)).toLocaleString();

        chart.data.labels = data.activityChart.map(row => row.date);
        chart.data.datasets[0].data = data.activityChart.map(row => row.total);
        chart.update();
      } catch (error) {
        console.error('Impossible de rafraîchir les statistiques.', error);
      }
    };

    window.setInterval(refreshStats, 15000);
  });
</script>
@endpush
