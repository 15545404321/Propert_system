Vue.component('Update', {
	template: `
		<el-dialog title="修改记录" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="原始房主" prop="member_id">
							<el-select @change="selectFcxxandCewei"  style="width:100%" v-model="form.member_id" filterable clearable placeholder="请选择原始房主">
								<el-option v-for="(item,i) in member_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="新任房主" prop="member_idb">
							<el-select   style="width:100%" v-model="form.member_idb" filterable clearable placeholder="请选择新任房主">
								<el-option v-for="(item,i) in member_idbs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="过户时间" prop="ghjl_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.ghjl_time" clearable placeholder="请输入过户时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用结算" prop="ghjl_jiesuan">
							<el-radio-group v-model="form.ghjl_jiesuan">
								<el-radio :label="1">原房主</el-radio>
								<el-radio :label="2">新房主</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="过户住宅" prop="fcxx_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.fcxx_id" :options="fcxx_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择过户住宅"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位信息" prop="cewei_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.cewei_id" :options="cewei_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择车位信息"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				member_id:'',
				member_idb:'',
				ghjl_time:curentTime(),
				ghjl_jiesuan:1,
				shop_id:'',
				xqgl_id:'',
			},
			member_ids:[],
			member_idbs:[],
			fcxx_ids:[],
			cewei_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Ghjl/getFcxx_id',{member_id:this.info.member_id}).then(res => {
					if(res.data.status == 200){
						this.fcxx_ids = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Ghjl/getCewei_id',{member_id:this.info.member_id}).then(res => {
					if(res.data.status == 200){
						this.cewei_ids = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Ghjl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.member_ids = res.data.data.member_ids
						this.member_idbs = res.data.data.member_idbs
						// this.cewei_ids = res.data.data.cewei_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.ghjl_time = parseTime(this.form.ghjl_time)
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
		},
		selectFcxxandCewei(val){
			this.selectFcxx_id(val)
			this.selectCewei_id(val)
		},
		selectFcxx_id(val){
			this.$delete(this.form,'fcxx_id')
			axios.post(base_url + '/Ghjl/getFcxx_id',{member_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		selectCewei_id(val){
			this.$delete(this.form,'cewei_id')
			axios.post(base_url + '/Ghjl/getCewei_id',{member_id:val}).then(res => {
				if(res.data.status == 200){
					this.cewei_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Ghjl/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
