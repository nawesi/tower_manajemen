<form method="POST" action="{{ route('logout') }}" class="d-inline">
  @csrf
  <button type="submit" class="btn btn-outline-dark btn-sm">
    Logout
  </button>
</form>
