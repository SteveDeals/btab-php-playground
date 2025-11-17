<form action="{{ route('btab.sync') }}" method="POST" style="margin-bottom:10px;">
    @csrf
    <button type="submit" class="btn btn-primary">
        🔄 Sync Products
    </button>
</form>
