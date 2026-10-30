<template>
  <div class="login-wrap">
    <div class="login-box">
      <div class="login-title">研学供应商</div>
      <el-form ref="form" :model="form" :rules="rules" @keyup.enter.native="handleSubmit">
        <el-form-item prop="account">
          <el-input v-model="form.account" size="large" placeholder="请输入账号" clearable>
            <i slot="prefix" class="el-icon-user"></i>
          </el-input>
        </el-form-item>
        <el-form-item prop="password">
          <el-input v-model="form.password" size="large" type="password" placeholder="请输入密码" show-password>
            <i slot="prefix" class="el-icon-lock"></i>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" size="large" :loading="loading" class="login-btn" @click="handleSubmit">
            登 录
          </el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
import { supplierLogin } from '@/api/login';
import { setToken, setSupplierInfo } from '@/libs/util';

export default {
  name: 'login',
  data() {
    return {
      loading: false,
      form: {
        account: '',
        password: '',
      },
      rules: {
        account: [{ required: true, message: '请输入账号', trigger: 'blur' }],
        password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
      },
    };
  },
  methods: {
    handleSubmit() {
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        this.loading = true;
        supplierLogin(this.form)
          .then((res) => {
            const data = res.data || {};
            setToken(data.token, data.exp_time);
            setSupplierInfo(data.supplierInfo || {});
            this.$message.success('登录成功');
            this.$router.replace({ name: 'project' });
          })
          .catch((res) => {
            this.$message.error((res && res.msg) || '登录失败');
          })
          .finally(() => {
            this.loading = false;
          });
      });
    },
  },
};
</script>
<style scoped>
.login-wrap {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2b6de8 0%, #1e9fff 100%);
}
.login-box {
  width: 380px;
  padding: 40px 36px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}
.login-title {
  font-size: 22px;
  font-weight: 600;
  text-align: center;
  margin-bottom: 28px;
  color: #303133;
}
.login-btn {
  width: 100%;
}
</style>
