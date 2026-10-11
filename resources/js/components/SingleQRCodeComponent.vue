<template>
  <div>
    <div class="visible-print text-center">
      {!! QrCode::size(100)->generate(1234); !!}
      <h2>
        <strong>{{ $res["qrc"] }}</strong>
      </h2>
      <p>{{ $res["customer_name"] }} #{{ $res["customer_id"] }}</p>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
</style>