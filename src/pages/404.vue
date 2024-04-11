<template>
  <f7-page>
    <f7-navbar title="Not found" back-link="Back"></f7-navbar>
    <f7-block strong inset>
      <p>Sorry</p>
      <p>Requested camera not found.</p>
    </f7-block>
  </f7-page>
</template>
<script>
export default {
	props: {
		f7route: Object,
		f7router: Object,
	}
};
</script>
