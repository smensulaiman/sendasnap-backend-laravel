@extends('layouts.dashboard')

@section('title', 'ড্যাশবোর্ড')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-1 bangla-font">স্বাগতম, {{ $user->name }}!</h2>
                    <p class="text-muted mb-0 bangla-font">আজ আপনার বই এবং বিনিময় সম্পর্কে এখানে দেখুন।</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="icon primary">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_books'] }}</h3>
                <p class="text-muted mb-0 bangla-font">মোট বই</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="icon secondary">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_swaps_sent'] }}</h3>
                <p class="text-muted mb-0 bangla-font">প্রেরিত বিনিময়</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="icon success">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_swaps_received'] }}</h3>
                <p class="text-muted mb-0 bangla-font">প্রাপ্ত বিনিময়</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="mb-1">{{ $stats['pending_swaps'] }}</h3>
                <p class="text-muted mb-0 bangla-font">অপেক্ষমান বিনিময়</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0 bangla-font">বইয়ের বিভাগ অনুযায়ী বিতরণ</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0 bangla-font">বিনিময়ের অবস্থা</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="swapStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Books Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 bangla-font">আমার সাম্প্রতিক বই</h5>
                    <a href="{{ route('dashboard.books.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i><span class="bangla-font">বই যোগ করুন</span>
                    </a>
                </div>
                <div class="card-body">
                    @if($userBooks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($userBooks as $book)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center">
                                            @if($book->photo_path)
                                                <img src="{{ $book->photo_path }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="table-book-image me-3">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center table-book-image">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                            @endif
                                        <div>
                                            <h6 class="mb-0">{{ $book->title }}</h6>
                                            <small class="text-muted">{{ $book->category->name }}</small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('dashboard.books.show', $book) }}">
                                                <i class="fas fa-eye me-2"></i><span class="bangla-font">দেখুন</span>
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('dashboard.books.edit', $book) }}">
                                                <i class="fas fa-edit me-2"></i><span class="bangla-font">সম্পাদনা</span>
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('dashboard.books.index') }}" class="btn btn-outline"><span class="bangla-font">সব বই দেখুন</span></a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted bangla-font">এখনো কোনো বই নেই</h6>
                            <p class="text-muted mb-3 bangla-font">প্ল্যাটফর্মে আপনার প্রথম বই যোগ করে শুরু করুন।</p>
                            <a href="{{ route('dashboard.books.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i><span class="bangla-font">আপনার প্রথম বই যোগ করুন</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Swaps Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 bangla-font">অপেক্ষমান বিনিময়</h5>
                    <a href="{{ route('dashboard.swaps.index') }}" class="btn btn-outline btn-sm"><span class="bangla-font">সব দেখুন</span></a>
                </div>
                <div class="card-body">
                    @if($receivedSwaps->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($receivedSwaps as $swap)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge badge-warning me-2 bangla-font">অপেক্ষমান</span>
                                                <small class="text-muted">{{ $swap->created_at->diffForHumans() }}</small>
                                            </div>
                                            <h6 class="mb-1 bangla-font">{{ $swap->requester->name }} বিনিময় করতে চান</h6>
                                            <p class="mb-1 text-muted bangla-font">
                                                <strong>{{ $swap->bookOffered->title }}</strong> 
                                                এর বিনিময়ে <strong>{{ $swap->bookRequested->title }}</strong>
                                            </p>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('dashboard.swaps.show', $swap) }}">
                                                    <i class="fas fa-eye me-2"></i><span class="bangla-font">বিস্তারিত দেখুন</span>
                                                </a></li>
                                                <li>
                                                    <form method="POST" action="{{ route('dashboard.swaps.update-status', $swap) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check me-2"></i><span class="bangla-font">গ্রহণ করুন</span>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('dashboard.swaps.update-status', $swap) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="declined">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-times me-2"></i><span class="bangla-font">প্রত্যাখ্যান করুন</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted bangla-font">কোনো অপেক্ষমান বিনিময় নেই</h6>
                            <p class="text-muted mb-0 bangla-font">এই মুহূর্তে আপনার কোনো অপেক্ষমান বিনিময় অনুরোধ নেই।</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- My Swap Requests -->
    @if($userSwaps->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 bangla-font">আমার বিনিময় অনুরোধ</h5>
                    <a href="{{ route('dashboard.swaps.index') }}" class="btn btn-outline btn-sm"><span class="bangla-font">সব দেখুন</span></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="bangla-font">অনুরোধিত বই</th>
                                    <th class="bangla-font">প্রদত্ত বই</th>
                                    <th class="bangla-font">মালিক</th>
                                    <th class="bangla-font">অবস্থা</th>
                                    <th class="bangla-font">তারিখ</th>
                                    <th class="bangla-font">কর্ম</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userSwaps as $swap)
                                    <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($swap->bookRequested->photo_path)
                                                        <img src="{{ $swap->bookRequested->photo_path }}" 
                                                             alt="{{ $swap->bookRequested->title }}" 
                                                             class="swap-book-image me-2">
                                                    @endif
                                                    <span>{{ $swap->bookRequested->title }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($swap->bookOffered->photo_path)
                                                        <img src="{{ $swap->bookOffered->photo_path }}" 
                                                             alt="{{ $swap->bookOffered->title }}" 
                                                             class="swap-book-image me-2">
                                                    @endif
                                                    <span>{{ $swap->bookOffered->title }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $swap->bookRequested->user->name }}</td>
                                            <td>
                                                @if($swap->status === 'pending')
                                                    <span class="badge badge-warning bangla-font">অপেক্ষমান</span>
                                                @elseif($swap->status === 'accepted')
                                                    <span class="badge badge-success bangla-font">গ্রহণ</span>
                                                @else
                                                    <span class="badge badge-danger bangla-font">প্রত্যাখ্যান</span>
                                                @endif
                                            </td>
                                            <td>{{ $swap->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.swaps.show', $swap) }}" class="btn btn-outline btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category Distribution Donut Chart
    const categoryCtx = document.getElementById('categoryDonutChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($categoryStats as $category)
                    '{{ $category->name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($categoryStats as $category)
                        {{ $category->books_count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#0a6d3a', // Primary green
                    '#35875b', // Secondary green
                    '#28a745', // Success green
                    '#20c997', // Teal
                    '#17a2b8', // Info blue
                    '#6f42c1', // Purple
                    '#e83e8c', // Pink
                    '#fd7e14', // Orange
                    '#ffc107', // Warning yellow
                    '#dc3545', // Danger red
                    '#6c757d', // Secondary gray
                    '#343a40', // Dark gray
                    '#007bff', // Primary blue
                    '#6610f2'  // Indigo
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverBorderWidth: 3,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            family: 'var(--bangla-font)',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' বই (' + percentage + '%)';
                        }
                    },
                    titleFont: {
                        family: 'var(--bangla-font)'
                    },
                    bodyFont: {
                        family: 'var(--bangla-font)'
                    }
                }
            },
            cutout: '60%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });

    // Swap Status Donut Chart
    const swapCtx = document.getElementById('swapStatusChart').getContext('2d');
    const swapChart = new Chart(swapCtx, {
        type: 'doughnut',
        data: {
            labels: ['অপেক্ষমান', 'গ্রহণ', 'প্রত্যাখ্যান'],
            datasets: [{
                data: [
                    {{ $stats['pending_swaps'] }},
                    {{ $stats['accepted_swaps'] ?? 0 }},
                    {{ $stats['declined_swaps'] ?? 0 }}
                ],
                backgroundColor: [
                    '#ffc107', // Warning yellow for pending
                    '#28a745', // Success green for accepted
                    '#dc3545'  // Danger red for declined
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverBorderWidth: 3,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            family: 'var(--bangla-font)',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    },
                    titleFont: {
                        family: 'var(--bangla-font)'
                    },
                    bodyFont: {
                        family: 'var(--bangla-font)'
                    }
                }
            },
            cutout: '60%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });

    // Add hover effects to charts
    [categoryChart, swapChart].forEach(chart => {
        chart.canvas.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
        
        chart.canvas.addEventListener('mouseleave', function() {
            this.style.cursor = 'default';
        });
    });
});
</script>
@endpush
